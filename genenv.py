import secrets

VARIABLES = ["POSTGRES_PASSWORD"]
TOKEN_LENGTH=32
OUTPUT_FILE=".env"

def generate_token(length: int) -> str:
    return secrets.token_urlsafe(length)

def main():
    with open(OUTPUT_FILE, "w") as file:
        for var in VARIABLES:
            token = generate_token(TOKEN_LENGTH)
            file.write(f"{var}={token}\n")

if __name__ == "__main__":
    main()
