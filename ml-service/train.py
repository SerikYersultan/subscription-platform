import sys
import math
import pandas as pd
import joblib
from pathlib import Path

BASE_DIR = Path(__file__).parent
DATA_PATH = BASE_DIR / "data" / "merchant_match_training_data.csv"
MODEL_PATH = BASE_DIR / "models" / "merchant_match_model.pkl"

FEATURES = [
    "name_similarity_score",
    "amount_diff_percent",
    "same_currency",
    "days_from_expected_billing",
    "brand_match",
    "service_match",
]

MIN_ROWS_FOR_SPLIT = 50
MIN_ROWS_TO_TRAIN = 10


def load_data():
    if not DATA_PATH.exists():
        print(f"Training data not found at: {DATA_PATH}")
        print("Run export_training_data.py first.")
        sys.exit(0)
    df = pd.read_csv(DATA_PATH)
    print(f"Loaded {len(df)} rows from {DATA_PATH.name}")
    return df


def prepare(df):
    df = df.copy()
    df = df[df["label"].notna()].copy()
    df["label"] = df["label"].astype(int)

    for col in FEATURES:
        if col not in df.columns:
            df[col] = 0

    df[FEATURES] = df[FEATURES].fillna(0)

    print(f"Labeled rows: {len(df)}")
    print(f"  Positive (same subscription): {(df['label'] == 1).sum()}")
    print(f"  Negative (different):         {(df['label'] == 0).sum()}")
    return df


def train(df):
    from sklearn.ensemble import RandomForestClassifier
    from sklearn.linear_model import LogisticRegression
    from sklearn.model_selection import train_test_split
    from sklearn.metrics import classification_report

    X = df[FEATURES]
    y = df["label"]

    if len(df) < MIN_ROWS_TO_TRAIN:
        print(f"Not enough labeled data to train (have {len(df)}, need at least {MIN_ROWS_TO_TRAIN}).")
        print("Add more transactions and subscriptions, or collect user confirmation labels.")
        return

    if len(df) >= MIN_ROWS_FOR_SPLIT:
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
        do_eval = True
    else:
        X_train, y_train = X, y
        do_eval = False
        print(f"Dataset smaller than {MIN_ROWS_FOR_SPLIT} rows — skipping train/test split, training on all data.")

    model = RandomForestClassifier(n_estimators=100, random_state=42, class_weight="balanced")
    model.fit(X_train, y_train)
    print("Model trained (RandomForestClassifier, 100 estimators).")

    if do_eval:
        y_pred = model.predict(X_test)
        print("\nClassification report (test set):")
        print(classification_report(y_test, y_pred, target_names=["different", "same"]))

    MODEL_PATH.parent.mkdir(parents=True, exist_ok=True)
    joblib.dump(model, MODEL_PATH)
    print(f"\nModel saved to: {MODEL_PATH}")

    importances = sorted(zip(FEATURES, model.feature_importances_), key=lambda x: x[1], reverse=True)
    print("\nFeature importances:")
    for feat, imp in importances:
        print(f"  {feat:<35} {imp:.4f}")


def main():
    df = load_data()
    df = prepare(df)
    train(df)


if __name__ == "__main__":
    main()
