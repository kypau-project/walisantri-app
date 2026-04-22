# 📚 Wali Santri App — API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
Uses **Laravel Sanctum** Bearer Token authentication.
Include the token in all protected requests:
```
Authorization: Bearer {token}
```

---

## 🔐 Auth Endpoints

### POST `/api/login`
Login and receive access token.

**Request Body:**
```json
{
  "username": "wali1",
  "password": "password"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "user": {
      "id": 2,
      "name": "Ahmad Fauzi",
      "username": "wali1",
      "role": "wali"
    },
    "token": "1|abc123..."
  }
}
```

---

### POST `/api/register`
Register new wali santri account.

**Request Body:**
```json
{
  "name": "Nama Wali",
  "username": "newuser",
  "password": "password",
  "password_confirmation": "password",
  "student_name": "Nama Santri",
  "nis": "2024099",
  "class": "VII-A",
  "room": "Al-Fatihah",
  "gender": "L",
  "father_phone": "081234567890",
  "mother_phone": "081234567891"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "user": { ... },
    "student": { ... },
    "token": "2|xyz789..."
  }
}
```

---

### POST `/api/logout` 🔒
Revoke current token.

**Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil."
}
```

---

### GET `/api/user` 🔒
Get authenticated user info with student data.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Ahmad Fauzi",
    "username": "wali1",
    "role": "wali",
    "student": {
      "id": 1,
      "name": "Muhammad Rizki Fauzi",
      "nis": "2024001",
      "class": "VII-A",
      "room": "Al-Fatihah",
      ...
    }
  }
}
```

---

## 👤 Profile Endpoints

### GET `/api/profile` 🔒
Get student profile data.

### PUT `/api/profile` 🔒
Update student profile.

**Request Body:**
```json
{
  "name": "Updated Name",
  "father_phone": "081234567890",
  "mother_phone": "081234567891",
  "address": "Jl. Baru No. 10"
}
```

---

## 💰 Bill Endpoints

### GET `/api/bills` 🔒
List all bills (sorted: pending first, then overdue, then paid).

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "SPP Januari 2024",
      "amount": 750000,
      "paid_amount": 0,
      "remaining": 750000,
      "status": "pending",
      "formatted_amount": "Rp 750.000",
      "formatted_remaining": "Rp 750.000",
      "due_date": "2024-01-10",
      ...
    }
  ]
}
```

### POST `/api/bills/pay` 🔒
Pay a bill.

**Request Body:**
```json
{
  "bill_id": 1,
  "amount": 750000,
  "payment_method": "transfer"
}
```

**Payment Methods:** `transfer`, `cash`, `ewallet`

### GET `/api/bills/history` 🔒
Get paid bills history.

---

## 📊 Payment Endpoints

### GET `/api/payments` 🔒
List all payment transactions (paginated, 20 per page).

### GET `/api/payments/{id}` 🔒
Get payment detail.

---

## 💸 Savings Endpoints

### GET `/api/savings` 🔒
Get savings balance.

### POST `/api/savings/topup` 🔒
Top up savings.

**Request Body:**
```json
{
  "amount": 50000,
  "description": "Uang saku mingguan"
}
```

### GET `/api/savings/history` 🔒
Get savings transaction history (paginated).

---

## 🧪 Exam Endpoints

### GET `/api/exams` 🔒
List all exams assigned to student.

### POST `/api/exams/start` 🔒
Start an exam.

**Request Body:**
```json
{
  "exam_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Ujian dimulai.",
  "data": {
    "exam_url": "https://exam.uqi.ac.id/start",
    "duration_minutes": 90
  }
}
```

---

## 📘 Report Endpoints

### GET `/api/reports` 🔒
List published reports with grades.

### GET `/api/reports/download?report_id=1` 🔒
Download report as PDF.

---

## 🔑 Demo Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Wali Santri 1 | `wali1` | `password` |
| Wali Santri 2 | `wali2` | `password` |
| Wali Santri 3 | `wali3` | `password` |
| Wali Santri 4 | `wali4` | `password` |
| Wali Santri 5 | `wali5` | `password` |

---

## Error Responses

**401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden:**
```json
{
  "message": "Unauthorized. Admin access required."
}
```

**422 Validation Error:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```
