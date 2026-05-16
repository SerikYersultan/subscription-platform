# ML Service — Merchant Matching

Isolated ML training pipeline for predicting whether two merchant names belong to the same subscription.

**Not connected to the production application yet.** This module only reads from the database and trains a local model file.

---

## What this module does

Bank statements sometimes show the same subscription under different merchant names:

- `NETFLIX.COM` and `NETFLIX INTERNATIONAL B.V.` → likely the same subscription
- `SPOTIFY` and `SPOTIFY AB STOCKHOLM` → likely the same subscription
- `YANDEX TAXI` and `YANDEX SUBSCRIPTION` → **not** the same subscription

This module trains a binary classifier that predicts whether two merchant names refer to the same subscription, based on features like name similarity, amount difference, and billing cycle alignment.

---

## Setup

```bash
cd ml-service
python -m venv venv
source venv/bin/activate        # Windows: venv\Scripts\activate
pip install -r requirements.txt
```

---

## Database configuration

Copy `.env.example` to `.env` and fill in your database details:

```bash
cp .env.example .env
```

The scripts will first try to load `ml-service/.env`, then fall back to the Laravel root `.env`.

For SQLite (default Laravel setup), the default path points to `../database/database.sqlite` relative to the `ml-service/` folder, which resolves to the Laravel project's own SQLite file.

---

## Export training data

```bash
python export_training_data.py
```

Reads from `transactions` and `subscriptions` tables. Generates candidate (subscription, transaction) pairs per user, computes features, assigns weak labels, and saves:

```
ml-service/data/merchant_match_training_data.csv
```

If a `merchant_match_feedback` table exists with user-confirmed labels, those override the weak labels automatically.

---

## Train the model

```bash
python train.py
```

Loads the CSV, removes unlabeled rows, trains a `RandomForestClassifier`, and saves:

```
ml-service/models/merchant_match_model.pkl
```

If fewer than 50 rows exist, the split into train/test is skipped and the model trains on all available data. If fewer than 10 labeled rows exist, the script exits with a message instead of crashing.

---

## Features used by the model

| Feature | Description |
|---|---|
| `name_similarity_score` | Fuzzy match score between merchant names (0–1) |
| `amount_diff_percent` | Relative difference in transaction amounts |
| `same_currency` | 1 if currencies match, 0 otherwise |
| `days_from_expected_billing` | How far the transaction is from the expected billing date (ratio) |
| `brand_match` | 1 if the first token (brand) of both names matches |
| `service_match` | 1 if service keywords (taxi, music, etc.) match |

---

## Improving accuracy

The model's accuracy depends directly on label quality.

**Weak labels** (generated automatically) are based on heuristics and are not fully reliable. They work as a starting point.

**Confirmed labels** (from `merchant_match_feedback`) are high-quality and should eventually replace weak labels for all pairs.

To collect confirmed labels, add a `merchant_match_feedback` table to the Laravel app and let users confirm or reject merchant matches through the UI. Re-run `export_training_data.py` after collecting feedback.

Suggested feedback table columns:
- `old_subscription_id` (foreign key → subscriptions)
- `new_transaction_id` (foreign key → transactions)
- `label` (1 = same, 0 = different)
- `confirmed_by_user_id`
- `confirmed_at`

---

## Output locations

| File | Description |
|---|---|
| `data/merchant_match_training_data.csv` | Training dataset with features and labels |
| `models/merchant_match_model.pkl` | Trained classifier (joblib format) |
