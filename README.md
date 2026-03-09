# LoanApp 🏦

A robust loan management system built with **Symfony 7**, designed to manage borrowers, loan applications, and financial reporting.

## ✨ Features
- **Admin Dashboard:** Powered by EasyAdmin for managing customers and loans.
- **Role-Based Security:** Separate access for Super Admins, Admins, and Borrowers.
- **Automated Registration:** Converts new users into customer profiles automatically.
- **Docker Integration:** Pre-configured PostgreSQL 16 environment.
- **API Ready:** Custom service-based calculation endpoints.

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Docker & Docker Desktop
- Symfony CLI

### Installation
1. **Clone the repository:**
   ```bash
   git clone https://github.com
   cd LoanApp
   docker compose up -d
   composer install
   php bin/console doctrine:migrations:migrate
   symfony serve

##🛠️ Tech Stack
Framework: Symfony 7
Database: PostgreSQL
Admin Panel: EasyAdmin 4
Containerization: Docker




