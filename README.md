# 🚀 Deploy Web App on EC2 with Docker Compose

This project demonstrates how to deploy a Dockerized web application (PHP + MySQL) on an AWS EC2 instance using Docker Compose.

---

## 🚀 1. Launch EC2 Instance

| Setting | Value |
|---|---|
| AMI | Amazon Linux 2023 |
| Instance Type | t3.micro (Free Tier) |
| Key Pair | Download `.pem` file |

**Security Group Rules:**

| Type | Port | Source |
|---|---|---|
| SSH | 22 | Your IP |
| HTTP/App | 8080 | 0.0.0.0/0 |

---

## 🔗 2. Connect to EC2

```bash
chmod 400 your-key.pem
ssh -i your-key.pem ec2-user@<EC2-PUBLIC-IP>
```

---

## 🐳 3. Install Docker & Docker Compose

```bash
sudo yum update -y
sudo yum install docker -y

sudo systemctl start docker
sudo systemctl enable docker

sudo usermod -aG docker ec2-user
newgrp docker
```

**Install Docker Compose:**

```bash
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" \
  -o /usr/local/bin/docker-compose

sudo chmod +x /usr/local/bin/docker-compose
```

**Verify Installation:**

```bash
docker --version
docker-compose --version
```

---

## 📁 4. Upload Project to EC2

Copy your local project folder to EC2 (run this from your local machine):

```bash
scp -i your-key.pem -r webdocker ec2-user@<EC2-PUBLIC-IP>:/home/ec2-user/
```

Navigate into the project directory on EC2:

```bash
cd webdocker/webdocker
```

---

## ▶️ 5. Run Application

```bash
docker-compose up --build -d
```

Check running containers:

```bash
docker ps
```

---

## 🌍 6. Access Application

Open your browser and visit:

```
http://<EC2-PUBLIC-IP>:8080
```

---

## 🗄️ Database Info

| Field | Value |
|---|---|
| Host | `mysqldb` |
| Database | `crudapp` |
| User | `cruduser` |
| Password | `crudpassword` |
| Root Password | `rootpassword` |

---

## 🧪 MySQL Access

### Step 1 — Enter the MySQL Container

```bash
docker exec -it <container_id> sh
```

> Get the container ID from `docker ps` under the `mysqldb` container.

### Step 2 — Login to MySQL

```bash
mysql -u root -p
# Enter password: rootpassword
```

---

### 📋 Common MySQL Commands

**Show all databases:**
```sql
SHOW DATABASES;
```

Output:
```
+--------------------+
| Database           |
+--------------------+
| crudapp            |
| information_schema |
| mysql              |
| performance_schema |
| sys                |
+--------------------+
5 rows in set (0.03 sec)
```

---

**Select the app database:**
```sql
USE crudapp;
```

Output:
```
Database changed
```

---

**Show all tables:**
```sql
SHOW TABLES;
```

Output:
```
+--------------------+
| Tables_in_crudapp  |
+--------------------+
| employees          |
+--------------------+
1 row in set (0.00 sec)
```

---

**View all records in a table:**
```sql
SELECT * FROM employees;
```

Output:
```
+----+------------------+------------------------+-------------+------------------+-----------+---------------------+---------------------+
| id | name             | email                  | department  | position         | salary    | created_at          | updated_at          |
+----+------------------+------------------------+-------------+------------------+-----------+---------------------+---------------------+
|  1 | Alice Johnson    | alice@example.com      | Engineering | Senior Developer | 85000.00  | 2026-05-10 15:58:04 | 2026-05-10 15:58:04 |
|  2 | Bob Smith        | bob@example.com        | Marketing   | Marketing Manager| 72000.00  | 2026-05-10 15:58:04 | 2026-05-10 15:58:04 |
|  3 | Carol White      | carol@example.com      | HR          | HR Specialist    | 60000.00  | 2026-05-10 15:58:04 | 2026-05-10 15:58:04 |
|  4 | Rituraj Chaudhary| manojchy4164@gmail.com | CSIT        | DevOps Engineer  | 50000.00  | 2026-05-10 15:58:32 | 2026-05-10 15:58:32 |
+----+------------------+------------------------+-------------+------------------+-----------+---------------------+---------------------+
4 rows in set (0.00 sec)
```

