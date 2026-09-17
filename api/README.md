# API Documentation

### Gateway & Router Base URLs
- Direct Endpoint: `http://<domain>/api/<module>.php`
- Gateway Route: `http://<domain>/api/?endpoint=<module>`
- Auth Headers (for protected endpoints): `Authorization: Bearer <token>` or `X-Api-Token: <token>`

---

### 1. Authentication API (`/api/auth.php`)

#### 1.1 Send OTP
* **URL:** `/api/auth.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "send_otp",
  "mobile": "9876543210"
}
```
* **Response (Success):**
```json
{
  "success": true,
  "message": "OTP sent successfully to +91-9876543210",
  "debug_otp": "123456"
}
```
* **Response (Error):**
```json
{
  "success": false,
  "message": "Please enter a valid 10-digit mobile number starting with 6-9."
}
```

#### 1.2 Verify OTP & Login
* **URL:** `/api/auth.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "verify_otp",
  "mobile": "9876543210",
  "otp": "123456"
}
```
* **Response (Success):**
```json
{
  "success": true,
  "message": "Login successful!",
  "token": "bin_tok_a1b2c3d4e5...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "mobile": "9876543210",
    "email": "user@example.com",
    "role": "customer"
  },
  "redirect_url": "http://.../index.php"
}
```
* **Response (Error):**
```json
{
  "success": false,
  "message": "Invalid or expired OTP code."
}
```

#### 1.3 Get Current User
* **URL:** `/api/auth.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "get_user"
}
```
* **Response:**
```json
{
  "success": true,
  "logged_in": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "mobile": "9876543210",
    "email": "user@example.com",
    "role": "customer"
  }
}
```

#### 1.4 Logout
* **URL:** `/api/auth.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "logout"
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Logged out successfully.",
  "redirect_url": "http://.../index.php"
}
```

---

### 2. User Profile & Address API (`/api/user.php`)
*(Requires Bearer Token in Authorization Header)*

#### 2.1 Update Profile
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "update_profile",
  "name": "John Doe",
  "email": "john@example.com"
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully!",
  "name": "John Doe",
  "email": "john@example.com"
}
```

#### 2.2 Get Saved Addresses
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "get_addresses"
}
```
* **Response:**
```json
{
  "success": true,
  "addresses": [
    {
      "id": "1",
      "user_id": "1",
      "title": "Home",
      "first_name": "John",
      "last_name": "Doe",
      "phone": "9876543210",
      "street_address": "123 Main St",
      "city": "Mumbai",
      "state": "Maharashtra",
      "postcode": "400001",
      "is_default": "1"
    }
  ]
}
```

#### 2.3 Get Address by ID
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "get_address",
  "address_id": 1
}
```
* **Response:**
```json
{
  "success": true,
  "address": {
    "id": "1",
    "user_id": "1",
    "title": "Home",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "9876543210",
    "street_address": "123 Main St",
    "city": "Mumbai",
    "state": "Maharashtra",
    "postcode": "400001",
    "is_default": "1"
  }
}
```

#### 2.4 Save Address (Create / Update)
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "save_address",
  "address_id": 0,
  "title": "Home",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "9876543210",
  "street_address": "123 Main St",
  "city": "Mumbai",
  "state": "Maharashtra",
  "postcode": "400001",
  "is_default": 1
}
```
* **Response:**
```json
{
  "success": true,
  "message": "New address saved successfully!"
}
```

#### 2.5 Delete Address
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "delete_address",
  "address_id": 1
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Address deleted successfully!"
}
```

#### 2.6 Set Default Address
* **URL:** `/api/user.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "set_default_address",
  "address_id": 1
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Default address set successfully!"
}
```

---

### 3. Products & Catalog API (`/api/products.php`)

#### 3.1 List & Filter Products
* **URL:** `/api/products.php`
* **Method:** `GET` / `POST`
* **Payload:**
```json
{
  "action": "list_products",
  "categories": "CategoryName",
  "sizes": "M,L",
  "colors": "Black",
  "brands": "BrandName",
  "search": "shirt",
  "min_price": 100,
  "max_price": 5000,
  "sort": "newest",
  "page": 1,
  "per_page": 12
}
```
* **Response:**
```json
{
  "success": true,
  "page": 1,
  "per_page": 12,
  "total_records": 45,
  "total_pages": 4,
  "products": [
    {
      "id": 10,
      "item_code": "ITEM001",
      "item_name": "Product Name",
      "group_name": "Category",
      "brand_name": "Brand",
      "description": "Description",
      "price": 999.00,
      "image": "http://.../image.jpg",
      "sizes": ["M", "L"],
      "total_stock": 50,
      "in_stock": true
    }
  ]
}
```

#### 3.2 Get Product Details
* **URL:** `/api/products.php`
* **Method:** `GET` / `POST`
* **Payload:**
```json
{
  "action": "get_product",
  "id": 10
}
```
* **Response:**
```json
{
  "success": true,
  "product": {
    "id": 10,
    "item_code": "ITEM001",
    "item_name": "Product Name",
    "group_name": "Category",
    "brand_name": "Brand",
    "description": "Description",
    "main_image": "http://.../img1.jpg",
    "gallery": [
      "http://.../img1.jpg",
      "http://.../img2.jpg"
    ],
    "price": 999.00,
    "sizes": ["M", "L"],
    "colors": ["Red", "Blue"],
    "variants": [
      {
        "variant_id": 1,
        "size": "M",
        "color": "Red",
        "price": 999.00,
        "quantity": 10,
        "in_stock": true
      }
    ],
    "related_products": [
      {
        "id": 12,
        "name": "Related Product",
        "group_name": "Category",
        "price": 799.00,
        "image": "http://.../rel.jpg"
      }
    ]
  }
}
```

#### 3.3 Get Catalog Filters
* **URL:** `/api/products.php`
* **Method:** `GET` / `POST`
* **Payload:**
```json
{
  "action": "get_filters"
}
```
* **Response:**
```json
{
  "success": true,
  "filters": {
    "categories": [{"name": "Category 1", "count": 10}],
    "brands": [{"name": "Brand 1", "count": 5}],
    "sizes": ["S", "M", "L", "XL"],
    "colors": ["Black", "White"],
    "min_price": 100.00,
    "max_price": 5000.00
  }
}
```

---

### 4. Shopping Cart API (`/api/cart.php`)

#### 4.1 Get Cart
* **URL:** `/api/cart.php`
* **Method:** `GET` / `POST`
* **Payload:**
```json
{
  "action": "get_cart"
}
```
* **Response:**
```json
{
  "success": true,
  "items": [
    {
      "id": 1,
      "product_id": 10,
      "product_name": "Product Name",
      "size": "M",
      "price": 999.00,
      "quantity": 2,
      "row_total": 1998.00,
      "image_url": "http://.../img.jpg"
    }
  ],
  "summary": {
    "total_qty": 2,
    "subtotal": 1998.00,
    "shipping": 100.00,
    "gst_rate": 5.00,
    "gst_amount": 104.90,
    "grand_total": 2202.90
  }
}
```

#### 4.2 Add to Cart
* **URL:** `/api/cart.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "add_to_cart",
  "product_id": 10,
  "quantity": 1,
  "size": "M"
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Product Name (Size: M) added to cart.",
  "items": [...],
  "summary": {...}
}
```

#### 4.3 Update Item Quantity
* **URL:** `/api/cart.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "update_quantity",
  "id": 1,
  "quantity": 3
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Cart updated.",
  "items": [...],
  "summary": {...}
}
```

#### 4.4 Remove Item from Cart
* **URL:** `/api/cart.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "remove_item",
  "id": 1
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Item removed from cart.",
  "items": [...],
  "summary": {...}
}
```

#### 4.5 Clear Cart
* **URL:** `/api/cart.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "clear_cart"
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Cart cleared.",
  "items": [],
  "summary": {
    "total_qty": 0,
    "subtotal": 0,
    "shipping": 0,
    "gst_rate": 5,
    "gst_amount": 0,
    "grand_total": 0
  }
}
```

---

### 5. Orders & Checkout API (`/api/orders.php`)

#### 5.1 Submit New Order
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "process_order",
  "first_name": "John",
  "last_name": "Doe",
  "street_address": "123 Main St",
  "city": "Mumbai",
  "postcode": "400001",
  "phone": "9876543210",
  "payment_method": "cod",
  "ship_to_different": 0,
  "order_notes": ""
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Order created successfully.",
  "order_id": 105
}
```

#### 5.2 Create Razorpay Order
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "create_razorpay_order",
  "payment_method": "razorpay"
}
```
* **Response:**
```json
{
  "success": true,
  "order_id": "order_Kxxxxxxxxx",
  "amount": 220290,
  "key": "rzp_test_xxxxxx"
}
```

#### 5.3 Verify Payment
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "verify_payment",
  "order_id": 105,
  "razorpay_payment_id": "pay_Kxxxxxxxxx",
  "razorpay_order_id": "order_Kxxxxxxxxx",
  "razorpay_signature": "5f9c..."
}
```
* **Response:**
```json
{
  "success": true,
  "message": "Payment verified successfully!",
  "order_id": 105,
  "payment_status": "paid",
  "awb": "1234567890"
}
```

#### 5.4 Get My Orders
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Headers:** `Authorization: Bearer <token>`
* **Payload:**
```json
{
  "action": "get_my_orders"
}
```
* **Response:**
```json
{
  "success": true,
  "orders": [
    {
      "order_id": "105",
      "first_name": "John",
      "last_name": "Doe",
      "phone": "9876543210",
      "grand_total": "2202.90",
      "payment_method": "cod",
      "payment_status": "pending",
      "order_status": "pending",
      "created_at": "2026-09-08 00:00:00",
      "items": [
        {
          "id": "1",
          "product_id": "10",
          "product_title": "Product Name",
          "qty": "2",
          "size": "M",
          "price": "999.00",
          "row_total": "1998.00",
          "image_url": "http://.../img.jpg"
        }
      ]
    }
  ]
}
```

#### 5.5 Get Order Details
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "get_order_details",
  "order_id": 105
}
```
* **Response:**
```json
{
  "success": true,
  "order": {
    "order_id": "105",
    "first_name": "John",
    "last_name": "Doe",
    "street_address": "123 Main St",
    "city": "Mumbai",
    "postcode": "400001",
    "phone": "9876543210",
    "subtotal": "1998.00",
    "gst_amount": "104.90",
    "grand_total": "2202.90",
    "payment_method": "cod",
    "payment_status": "pending",
    "order_status": "pending",
    "created_at": "2026-09-08 00:00:00"
  },
  "items": [...]
}
```

#### 5.6 Track Order
* **URL:** `/api/orders.php`
* **Method:** `POST` / `GET`
* **Payload:**
```json
{
  "action": "track_order",
  "query": "105"
}
```
* **Response:**
```json
{
  "success": true,
  "order": {...},
  "items": [...],
  "awb": "1234567890",
  "courier_name": "delhivery",
  "tracking": null
}
```
