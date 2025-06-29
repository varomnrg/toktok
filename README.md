# 📱 TokTok Toko Elektronik - Admin Management System

Toko Elektronik Website is a web application used to manage electronic product data. This application allows users to perform CRUD operations (Create, Read, Update, Delete) on electronic products. It also includes a user authentication feature to ensure data security. This application was created as part of the BNSP Jobhun certification test assignment.

## 🚀 Features

- **User Authentication** - Secure login system with password hashing
- **Product Management** - Full CRUD operations (Create, Read, Update, Delete)
- **Dashboard Analytics** - Visual charts showing inventory statistics
- **Responsive Design** - Modern UI built with TailwindCSS
- **Modal Interface** - Smooth animations for add/edit product forms
- **Data Validation** - Input sanitization and error handling
- **Stock Management** - Track product quantities and values

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/varomnrg/toktok
   cd toktok
   ```

2. **Set up the database**
   ```bash
   mysql -u root -p < data.sql
   ```

3. **Configure environment (optional)**
   Create a .env file for custom database settings:
   ```
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=your_password
   DB_NAME=toko_elektronik
   ```

4. **Start the development server**
   ```bash
   chmod +x dev.sh
   ./dev.sh
   ```
   Or manually:
   ```bash
   php -S localhost:8000
   ```

5. **Access the application**
   Open your browser and go to: `http://localhost:8000`

## 🔐 Default Login Credentials

- **Username:** `admin`
- **Password:** `admin123`

## Author
[Timothy Aurelio Cannavaro](https://github.com/varomnrg)
