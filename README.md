# Full-Stack-Ecommerce-PHP

## Overview
This project is a full-stack E-Commerce website developed using PHP, MySQL, HTML5, CSS3, and JavaScript.  
The system allows users to browse products, view product details, manage a shopping cart, and place orders through a responsive and user-friendly interface.

The project is designed to simulate a real online shopping platform with both front-end and back-end functionality, including database integration and user interaction features.

---

# Project Goals
- Build a responsive and modern e-commerce website.
- Practice full-stack web development concepts.
- Implement database relationships and dynamic content rendering.
- Create reusable and organized PHP components.
- Manage shopping cart operations and order processing.

---

# Technologies Used

## Front-End
- HTML5
- CSS3
- JavaScript
- Flexbox & CSS Grid

## Back-End
- PHP

## Database
- MySQL

## Tools
- XAMPP
- Git & GitHub
- VS Code

---

# Main Features

## Home Page
- Responsive navigation bar
- Hero section with promotional banner
- Featured products section
- Product search functionality
- Footer with contact and social links

## Products Page
- Dynamic product listing
- Category filtering
- Product sorting
- Product cards with add-to-cart functionality

## Product Details Page
- Detailed product information
- Product image and stock status
- Quantity selector
- Add to cart functionality

## Shopping Cart Page
- Update product quantities
- Remove items from cart
- Cart summary and totals
- Persistent cart storage

## Contact Page
- Contact form with validation
- User message submission
- Contact information section

---

# Database Structure

The system uses a relational MySQL database with the following tables:

- users
- categories
- products
- cart_items
- orders
- order_items
- contacts

The database handles:
- Product management
- User accounts
- Shopping cart persistence
- Order tracking
- Contact form submissions

---

# Authentication System
The project includes:
- User registration
- User login/logout
- Session handling
- Role management (User / Admin)

---

# Project Structure

```txt
ecommerce-project/
│
├── assets/
├── config/
├── includes/
├── pages/
├── auth/
├── admin/
├── api/
├── database/
├── index.php
└── README.md