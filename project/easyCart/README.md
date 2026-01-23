# EasyCart

EasyCart is a lightweight, file-based PHP e-commerce application designed to demonstrate core shopping functionalities without the need for a complex database backend. It uses PHP files to store arrays of data for products, brands, and categories.

## Features

- **Product Browsing**:
  - **Home Page**: Features highlighted products, categories, and brands.
  - **Product Listing Page (PLP)**: Browse all products with sidebar filters for **Brand** and **Price**.
  - **Product Detail Page (PDP)**: View detailed information, pricing, and features for individual products.
- **Shopping Cart**:
  - Add items to the cart from the PLP or PDP.
  - View cart summary and manage item quantities.
  - Session-based cart persistence.
- **Checkout**: A checkout page to review orders (frontend implementation).
- **Responsive Design**: Mobile-friendly layout using custom CSS.

## Project Structure

```
easyCart/
├── data/           # Data files (products.php, brands.php, categories.php)
├── images/         # Product images and icons
├── includes/       # Shared partials (header.php, footer.php, init.php)
├── pages/          # Application pages
│   ├── index.php   # Homepage
│   ├── plp.php     # Product Listing Page
│   ├── pdp.php     # Product Detail Page
│   ├── cart.php    # Shopping Cart
│   ├── checkout.php# Checkout Page
│   └── ...
└── style/
    └── style.css   # Main stylesheet
```

## Setup & Installation

1.  **Prerequisites**:
    - A local server environment with PHP support (e.g., [XAMPP](https://www.apachefriends.org/), [WAMP](https://www.wampserver.com/), or PHP built-in server).

2.  **Installation**:
    - Clone or download this repository.
    - Move the project folder to your server's root directory:
      - **XAMPP**: `C:\xampp\htdocs\easyCart`
      - **WAMP**: `C:\wamp64\www\easyCart`

3.  **Running the Application**:
    - Start your local web server (Apache).
    - Open your browser and navigate to:
      ```
      http://localhost/easyCart/pages/index.php
      ```

## Usage

- **Browsing**: Start at the homepage to see featured items.
- **Filtering**: Go to the "Shop" page (PLP) to filter products by brand or price range.
- **Buying**: Click "View Product" to read more or "Add to Cart" to select items.
- **Checkout**: Proceed to the cart and click "Checkout" to see the order summary.

## Technologies Used

- **PHP**: Core application logic and data handling.
- **HTML5 & CSS3**: Structure and styling (Grid/Flexbox).
- **JavaScript**: Simple client-side interactions (validations).
