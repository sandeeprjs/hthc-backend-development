import random
import pymysql
from faker import Faker

# Initialize Faker for realistic data
fake = Faker()

# Database connection
conn = pymysql.connect(
    host='127.0.0.1',        # Update with your DB host
    user='laraveluser',             # Update with your DB user
    password='password',     # Update with your DB password
    database='hthcdb' # Update with your DB name
)
cursor = conn.cursor()
def insert_roles():
    roles = ['Admin', 'Customer', 'Delivery Agent', 'Manager', 'Franchisee']
    for role in roles:
        cursor.execute(
            "INSERT INTO roles (name, description, created_at, updated_at) VALUES (%s, %s, NOW(), NOW())",
            (role, f"{role} role")
        )

def insert_branches():
    branches = [
        ('Whitefield Branch','Whitefield Branch', 'Bangalore', 1),
        ('Electronic City Branch','Electronic City Branch', 'Bangalore', 2),
        ('MG Road Branch','MG Road Branch', 'Bangalore', 3),
    ]
    for branch in branches:
        cursor.execute(
            "INSERT INTO branches (branch_name,name, city, pincode_id, created_at, updated_at) VALUES (%s,%s, %s, %s, NOW(), NOW())",
            branch
        )


def insert_pincodes():
    for _ in range(50):
        pincode = fake.postcode()
        city = fake.city()
        state = fake.state()
        cursor.execute(
            "INSERT INTO pincodes (pincode, city, state, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())",
            (pincode, city, state)
        )

def insert_subscriptions():
    subscriptions = [
        ('Standard Plan', 'standard', 100, 5),
        ('Express Plan', 'express', 200, 2),
    ]
    for subscription in subscriptions:
        cursor.execute(
            "INSERT INTO subscriptions (name, consg_type, price, max_delivery_time, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())",
            subscription
        )

def insert_pricings():
    pricings = [
        (0, 5, 50, 1, 10, 'standard'),
        (5, 10, 100, 1, 15, 'express'),
    ]
    for pricing in pricings:
        cursor.execute(
            "INSERT INTO pricings (from_weight_kgs, to_weight_kgs, price, addl_weight, addl_price, consg_type, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())",
            pricing
        )

def insert_users():
    for _ in range(100):
        first_name = fake.first_name()
        last_name = fake.last_name()
        email = fake.email()
        username = f"{first_name.lower()}.{last_name.lower()}"
        cursor.execute(
            "INSERT INTO users (username, first_name, last_name, email, password, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, NOW(), NOW())",
            (username, first_name, last_name, email, "hashed_password_here")
        )

def insert_bookings_and_deliveries():
    consg_numbers = set()  # Track generated consignment numbers to avoid duplicates
    for _ in range(50000):
        while True:  # Keep generating until a unique consg_number is found
            consg_number = f"CNSG{random.randint(1000, 999999)}"
            if consg_number not in consg_numbers:
                consg_numbers.add(consg_number)  # Mark consg_number as used
                break

        consg_type = random.choice(["Standard", "Express"])
        customer_id = random.randint(1, 100)
        subscription_id = random.randint(1, 10)
        cursor.execute(
            "INSERT INTO bookings (consg_number, consg_type, customer_id, subscription_id, created_at, updated_at) VALUES (%s, %s, %s, %s, NOW(), NOW())",
            (consg_number, consg_type, customer_id, subscription_id)
        )
        booking_id = cursor.lastrowid

        # Insert related deliveries
        for _ in range(random.randint(1, 5)):
            receiver_name = fake.name()
            address = fake.address()
            city = fake.city()
            state = fake.state()
            cursor.execute(
                "INSERT INTO deliveries (booking_id, receiver_name, add_line_1, city, state, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, NOW(), NOW())",
                (booking_id, receiver_name, address, city, state)
            )


def insert_manifests():
    for _ in range(20000):
        # Fetch a random consignment ID from the `consignments` table
        cursor.execute("SELECT id FROM consignments ORDER BY RAND() LIMIT 1")
        consignment = cursor.fetchone()

        if consignment:
            consg_number_id = consignment[0]  # Get the consignment ID
            manifest_number = f"MANIFEST{random.randint(10000, 99999)}"
            manifest_type = random.choice(["incoming", "outgoing"])
            cursor.execute(
                "INSERT INTO manifests (manifest_number, manifest_type, consg_number_id, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())",
                (manifest_number, manifest_type, consg_number_id)
            )


def insert_run_sheets():
    for _ in range(10000):
        user_id = random.randint(1, 100)
        cursor.execute(
            "INSERT INTO run_sheets (user_id, created_at, updated_at) VALUES (%s, NOW(), NOW())",
            (user_id,)
        )

# Execute all insert functions
try:
#     insert_roles()
#     insert_branches()
#     insert_pincodes()
#     insert_subscriptions()
#     insert_pricings()
#     insert_users()
    insert_bookings_and_deliveries()
    insert_manifests()
#     insert_run_sheets()
    conn.commit()
    print("Data successfully inserted!")
except Exception as e:
    conn.rollback()
    print(f"Error: {e}")
finally:
    cursor.close()
    conn.close()
