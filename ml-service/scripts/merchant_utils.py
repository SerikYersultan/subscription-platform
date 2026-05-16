import re
from rapidfuzz import fuzz

SERVICE_KEYWORDS = {
    "taxi", "eda", "music", "drive", "cloud", "plus", "premium", "pro",
    "subscription", "tv", "video", "pay", "wallet", "food", "delivery",
    "icloud", "arcade", "one", "pass", "now", "go", "prime", "unlimited",
    "basic", "standard", "student", "family", "business", "enterprise",
    "international", "global",
}

NOISE_PATTERNS = [
    r"\b\d{4,}\b",
    r"\b(ltd|llc|inc|corp|gmbh|b\.?v\.?|ab|as|oy|sa|ag|co)\b",
    r"[^\w\s]",
]

def normalize(name: str) -> str:
    if not isinstance(name, str):
        return ""
    name = name.lower().strip()
    for pattern in NOISE_PATTERNS:
        name = re.sub(pattern, " ", name)
    name = re.sub(r"\s+", " ", name).strip()
    return name

def extract_brand_and_service(name: str):
    tokens = normalize(name).split()
    if not tokens:
        return "", ""
    brand = tokens[0]
    services = [t for t in tokens[1:] if t in SERVICE_KEYWORDS]
    service = " ".join(services)
    return brand, service

def name_similarity(a: str, b: str) -> float:
    na, nb = normalize(a), normalize(b)
    if not na or not nb:
        return 0.0
    token_set = fuzz.token_set_ratio(na, nb) / 100.0
    partial = fuzz.partial_ratio(na, nb) / 100.0
    return round((token_set * 0.7 + partial * 0.3), 4)

def brand_matches(a: str, b: str) -> int:
    brand_a, _ = extract_brand_and_service(a)
    brand_b, _ = extract_brand_and_service(b)
    if not brand_a or not brand_b:
        return 0
    return int(brand_a == brand_b)

def service_matches(a: str, b: str) -> int:
    _, svc_a = extract_brand_and_service(a)
    _, svc_b = extract_brand_and_service(b)
    if not svc_a and not svc_b:
        return 1
    if not svc_a or not svc_b:
        return 0
    return int(svc_a == svc_b)
