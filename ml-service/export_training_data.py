import os
import sys
import math
import pandas as pd
from pathlib import Path
from dotenv import load_dotenv
from sqlalchemy import create_engine, text, inspect

sys.path.insert(0, str(Path(__file__).parent))
from scripts.merchant_utils import name_similarity, brand_matches, service_matches

BASE_DIR = Path(__file__).parent
load_dotenv(BASE_DIR / ".env", override=False)
load_dotenv(BASE_DIR.parent / ".env", override=False)

OUTPUT_PATH = BASE_DIR / "data" / "merchant_match_training_data.csv"


def build_engine():
    driver = os.getenv("DB_CONNECTION", "sqlite").lower()
    if driver == "sqlite":
        db_path = os.getenv("DB_DATABASE", "../database/database.sqlite")
        db_path = Path(db_path) if Path(db_path).is_absolute() else BASE_DIR / db_path
        db_path = db_path.resolve()
        if not db_path.exists():
            print(f"SQLite database not found at: {db_path}")
            sys.exit(1)
        return create_engine(f"sqlite:///{db_path}")
    if driver == "mysql":
        host = os.getenv("DB_HOST", "127.0.0.1")
        port = os.getenv("DB_PORT", "3306")
        db = os.getenv("DB_DATABASE", "")
        user = os.getenv("DB_USERNAME", "")
        pw = os.getenv("DB_PASSWORD", "")
        return create_engine(f"mysql+pymysql://{user}:{pw}@{host}:{port}/{db}")
    if driver in ("pgsql", "postgresql", "postgres"):
        host = os.getenv("DB_HOST", "127.0.0.1")
        port = os.getenv("DB_PORT", "5432")
        db = os.getenv("DB_DATABASE", "")
        user = os.getenv("DB_USERNAME", "")
        pw = os.getenv("DB_PASSWORD", "")
        return create_engine(f"postgresql+psycopg2://{user}:{pw}@{host}:{port}/{db}")
    print(f"Unsupported DB_CONNECTION: {driver}")
    sys.exit(1)


def table_exists(engine, table_name):
    return inspect(engine).has_table(table_name)


def load_feedback(engine):
    if not table_exists(engine, "merchant_match_feedback"):
        return pd.DataFrame()
    with engine.connect() as conn:
        return pd.read_sql(text("SELECT * FROM merchant_match_feedback"), conn)


def load_transactions(engine):
    with engine.connect() as conn:
        return pd.read_sql(
            text("SELECT id, user_id, merchant_id, merchant_name, amount, currency, transaction_date FROM transactions"),
            conn,
            parse_dates=["transaction_date"],
        )


def load_subscriptions(engine):
    with engine.connect() as conn:
        return pd.read_sql(
            text(
                "SELECT id, user_id, merchant_id, merchant_name, name, amount, currency, "
                "billing_cycle, confidence_score, next_billing_date, next_charge_date "
                "FROM subscriptions WHERE status = 'active'"
            ),
            conn,
            parse_dates=["next_billing_date", "next_charge_date"],
        )


def cycle_days(cycle):
    mapping = {"weekly": 7, "monthly": 30, "quarterly": 91, "yearly": 365}
    return mapping.get(cycle, 30)


def amount_diff_percent(a, b):
    if a is None or b is None or math.isnan(a) or math.isnan(b):
        return None
    if a == 0 and b == 0:
        return 0.0
    base = max(abs(a), abs(b))
    if base == 0:
        return 0.0
    return round(abs(float(a) - float(b)) / base, 4)


def days_from_billing(tx_date, next_billing, cycle):
    if pd.isna(tx_date) or pd.isna(next_billing):
        return None
    days = int((pd.Timestamp(tx_date) - pd.Timestamp(next_billing)).days)
    expected_interval = cycle_days(cycle)
    return round(abs(days) / expected_interval, 4)


def build_weak_pairs(subscriptions, transactions):
    rows = []
    sub_grouped = subscriptions.groupby("user_id")
    tx_grouped = transactions.groupby("user_id")

    for user_id, user_subs in sub_grouped:
        if user_id not in tx_grouped.groups:
            continue
        user_txs = tx_grouped.get_group(user_id)

        for _, sub in user_subs.iterrows():
            sub_name = sub["merchant_name"] or sub["name"] or ""
            if not sub_name:
                continue

            next_date = sub["next_charge_date"] if pd.notna(sub.get("next_charge_date")) else sub.get("next_billing_date")

            for _, tx in user_txs.iterrows():
                tx_name = tx["merchant_name"] or ""
                if not tx_name:
                    continue

                sim = name_similarity(sub_name, tx_name)
                amt_diff = amount_diff_percent(sub["amount"], tx["amount"])
                same_cur = int(str(sub["currency"]).upper() == str(tx["currency"]).upper())
                days_ratio = days_from_billing(tx["transaction_date"], next_date, sub.get("billing_cycle", "monthly"))
                brand_ok = brand_matches(sub_name, tx_name)
                service_ok = service_matches(sub_name, tx_name)

                if sim < 0.1:
                    continue

                if sim >= 0.8 and (amt_diff is None or amt_diff <= 0.05) and same_cur:
                    label = 1
                elif brand_ok and service_ok and sim >= 0.6:
                    label = 1
                elif brand_ok and not service_ok:
                    label = 0
                elif sim < 0.3:
                    label = 0
                else:
                    label = None

                rows.append({
                    "user_id": user_id,
                    "old_subscription_id": sub["id"],
                    "old_merchant_name": sub_name,
                    "new_transaction_id": tx["id"],
                    "new_merchant_name": tx_name,
                    "old_amount": sub["amount"],
                    "new_amount": tx["amount"],
                    "old_currency": sub["currency"],
                    "new_currency": tx["currency"],
                    "old_billing_cycle": sub.get("billing_cycle"),
                    "new_transaction_date": tx["transaction_date"],
                    "old_next_billing_date": next_date,
                    "name_similarity_score": sim,
                    "amount_diff_percent": amt_diff,
                    "same_currency": same_cur,
                    "days_from_expected_billing": days_ratio,
                    "old_category": None,
                    "new_category": None,
                    "brand_match": brand_ok,
                    "service_match": service_ok,
                    "billing_cycle_match": None,
                    "label": label,
                    "label_source": "weak",
                })

    return pd.DataFrame(rows)


def merge_feedback_labels(pairs_df, feedback_df):
    if feedback_df.empty:
        return pairs_df

    needed = {"old_subscription_id", "new_transaction_id", "label"}
    if not needed.issubset(feedback_df.columns):
        return pairs_df

    feedback_df = feedback_df[["old_subscription_id", "new_transaction_id", "label"]].copy()
    feedback_df.columns = ["old_subscription_id", "new_transaction_id", "confirmed_label"]

    merged = pairs_df.merge(feedback_df, on=["old_subscription_id", "new_transaction_id"], how="left")
    mask = merged["confirmed_label"].notna()
    merged.loc[mask, "label"] = merged.loc[mask, "confirmed_label"]
    merged.loc[mask, "label_source"] = "confirmed"
    merged.drop(columns=["confirmed_label"], inplace=True)
    return merged


def main():
    engine = build_engine()
    print("Connected to database.")

    transactions = load_transactions(engine)
    subscriptions = load_subscriptions(engine)
    feedback = load_feedback(engine)

    print(f"Loaded {len(transactions)} transactions, {len(subscriptions)} active subscriptions.")

    if transactions.empty or subscriptions.empty:
        print("Not enough data to generate training pairs. Add transactions and subscriptions first.")
        sys.exit(0)

    pairs = build_weak_pairs(subscriptions, transactions)
    print(f"Generated {len(pairs)} candidate pairs.")

    if not feedback.empty:
        pairs = merge_feedback_labels(pairs, feedback)
        confirmed = (pairs["label_source"] == "confirmed").sum()
        print(f"Applied {confirmed} confirmed labels from merchant_match_feedback.")

    OUTPUT_PATH.parent.mkdir(parents=True, exist_ok=True)
    pairs.to_csv(OUTPUT_PATH, index=False)
    print(f"Saved training data to: {OUTPUT_PATH}")
    print(f"Labeled rows: {pairs['label'].notna().sum()} / {len(pairs)}")


if __name__ == "__main__":
    main()
