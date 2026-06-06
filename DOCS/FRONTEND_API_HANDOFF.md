# Frontend API Handoff — EssenseLuxe Ecommerce API

> **Project**: EssenseLuxe Ecommerce API
>
> **Framework**: Laravel 10 (PHP ^8.1)
>
> **Auth**: Sanctum (Bearer Token)
>
> **Frontend Stack**: Laravel Blade + Tailwind CSS + Alpine.js + Axios (JavaScript)
>
> **Base URL**: `http://127.0.0.1:8000/api`

---

## 1. Project Overview

### What This API Is For
Customer-facing REST API for the EssenseLuxe ecommerce platform. Handles the complete customer journey: browsing products, managing cart, checkout, payments, order tracking, loyalty rewards, product reviews, and profile management.

### Customer-Facing Scope
All endpoints serve the **customer** (end-user). There is no admin functionality in this API. Admin operations (order processing, payment confirmation, product management) are handled by a separate admin panel.

### Authentication Method
Laravel Sanctum token-based authentication. Tokens are issued on register/login and sent as `Authorization: Bearer {token}` header for protected endpoints.

### Response Format Standard
All API responses follow a consistent JSON envelope:

```json
{
  "success": true,
  "message": "Operation message",
  "data": { ... }
}
```

List endpoints wrap `data` in a paginated structure:

```json
{
  "success": true,
  "message": "Daftar produk berhasil diambil",
  "data": [
    { ... },
    { ... }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 12,
    "total": 52
  }
}
```

### Error Format Standard
```json
{
  "success": false,
  "message": "Human-readable error message"
}
```

Validation errors include field-level errors:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email sudah terdaftar"]
  }
}
```

### Pagination Format
All paginated endpoints use Laravel's LengthAwarePaginator with this structure:

| Field | Type | Description |
|-------|------|-------------|
| `data` | array | Array of items |
| `meta.current_page` | integer | Current page |
| `meta.last_page` | integer | Last page number |
| `meta.per_page` | integer | Items per page |
| `meta.total` | integer | Total items across all pages |

---

## 2. Authentication

### Register
Creates a new customer account and returns a Bearer token.

```
POST /api/register
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "phone": "08123456789",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "08123456789",
      "point_balance": 0,
      "created_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "1|abc123..."
  }
}
```

**Validation Errors (422):**
- `name`: required, string, max:255
- `email`: required, valid email, unique
- `password`: required, min:8, must match `password_confirmation`
- `phone`: optional, string, max:20

---

### Login
Authenticates with email and password, returns a Bearer token. **Revokes all previous tokens** for this user.

```
POST /api/login
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "budi@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "08123456789",
      "point_balance": 5000,
      "created_at": "2025-01-01T00:00:00.000000Z"
    },
    "token": "2|def456..."
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Email atau password salah.",
  "errors": {
    "email": ["Email atau password salah."]
  }
}
```

> **Note**: Soft-deleted users cannot login. Returns 422 with `"Akun telah dinonaktifkan."`

---

### Logout
Revokes the current Bearer token.

```
POST /api/logout
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Logout berhasil",
  "data": null
}
```

---

### Get Authenticated User
Returns the authenticated user's profile data.

```
GET /api/me
Authorization: Bearer {token}
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "08123456789",
      "point_balance": 5000,
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  }
}
```

### Required Headers for Protected Endpoints
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Token Storage Recommendation
- Store the Bearer token in **localStorage** or **httpOnly cookies**.
- Use **Alpine.js `$store`** or a simple global JavaScript object to manage auth state in Blade views.
- Attach the token via Axios interceptor.
- On `401` response, clear the token and redirect to login.
- On logout, call `POST /api/logout` AND clear local token state.

---

## 3. Endpoint Summary Table

| Module | Method | Endpoint | Auth Required | Description |
|--------|--------|----------|:-----------:|-------------|
| **Auth** | POST | `/api/register` | No | Create account + get token |
| **Auth** | POST | `/api/login` | No | Login + get token |
| **Auth** | POST | `/api/logout` | Yes | Revoke current token |
| **Auth** | GET | `/api/me` | Yes | Get authenticated user |
| **Profile** | GET | `/api/profile` | Yes | View profile |
| **Profile** | PUT | `/api/profile` | Yes | Update profile |
| **Profile** | PUT | `/api/profile/password` | Yes | Change password |
| **Catalog** | GET | `/api/categories` | No | List categories |
| **Catalog** | GET | `/api/brands` | No | List brands |
| **Catalog** | GET | `/api/products` | No | List/search/filter products |
| **Product** | GET | `/api/products/{slug}` | No | Get product detail |
| **Product** | GET | `/api/products/{product}/reviews` | No | Get product reviews |
| **Cart** | GET | `/api/cart` | Yes | Get cart items |
| **Cart** | POST | `/api/cart/items` | Yes | Add item to cart |
| **Cart** | PUT | `/api/cart/items/{item}` | Yes | Update cart item quantity |
| **Cart** | DELETE | `/api/cart/items/{item}` | Yes | Remove cart item |
| **Checkout** | POST | `/api/checkout` | Yes | Create order from cart |
| **Payment** | GET | `/api/payment-accounts` | Yes | List active bank accounts |
| **Payment** | POST | `/api/orders/{order}/payment` | Yes | Upload payment proof |
| **Orders** | GET | `/api/orders` | Yes | List orders (filterable) |
| **Orders** | GET | `/api/orders/{order_number}` | Yes | Get order detail |
| **Cancel** | POST | `/api/orders/{order}/cancel` | Yes | Cancel order |
| **Shipment** | GET | `/api/orders/{order}/tracking` | Yes | Get shipment tracking |
| **Rewards** | GET | `/api/rewards/balance` | Yes | Get point balance |
| **Rewards** | GET | `/api/rewards/transactions` | Yes | Get point history |
| **Reviews** | POST | `/api/orders/{order}/reviews` | Yes | Submit product review |

> **Total: 27 endpoints** — 4 public, 23 protected. All implemented and working.

---

## 4. Detailed Endpoint Documentation

---

### Auth

---

#### POST /api/register

- **Auth**: Public
- **Description**: Create a new customer account with auto-created zero-balance loyalty points. Returns Bearer token.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `name` | string | Yes | max:255 |
| `email` | string | Yes | valid email, max:255, unique |
| `phone` | string | No | max:20 |
| `password` | string | Yes | min:8 |
| `password_confirmation` | string | Yes | must match password |

**Response (201):** See section 2 above.

**Frontend Notes:**
- Show password strength indicator (min 8 chars).
- Store token immediately after successful registration.
- Redirect user to home page or onboarding.

---

#### POST /api/login

- **Auth**: Public
- **Description**: Authenticate user. **All previous tokens are revoked.** Returns new Bearer token.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `email` | string | Yes | valid email |
| `password` | string | Yes | - |

**Response (200):** See section 2 above.

**Error (422):**
```json
{
  "message": "Email atau password salah.",
  "errors": { "email": ["Email atau password salah."] }
}
```

**Frontend Notes:**
- On 422, show inline error for email field.
- On success, replace old token with new one.
- Soft-deleted users get: `"Akun telah dinonaktifkan."`

---

#### POST /api/logout

- **Auth**: Bearer Token
- **Description**: Revoke the current access token.

**Response (200):**
```json
{ "success": true, "message": "Logout berhasil", "data": null }
```

**Frontend Notes:**
- Clear all local auth state and token after logout.
- Redirect to login page.

---

#### GET /api/me

- **Auth**: Bearer Token
- **Description**: Get authenticated user's data including point balance.

**Response (200):**
```json
{
  "success": true,
  "message": "Data user berhasil diambil",
  "data": {
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "08123456789",
      "point_balance": 5000,
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  }
}
```

**Frontend Notes:**
- Use this endpoint to hydrate auth state on page load.
- `point_balance` reflects current loyalty points.

---

### Profile

---

#### GET /api/profile

- **Auth**: Bearer Token
- **Description**: View profile data (same data as `GET /api/me` but in a profile context).

**Response (200):**
```json
{
  "success": true,
  "message": "Data profil berhasil diambil",
  "data": {
    "id": 1,
    "name": "Budi Santoso",
    "email": "budi@example.com",
    "phone": "08123456789",
    "point_balance": 5000,
    "created_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

> **Note**: Unlike `/api/me` which wraps user in `data.user`, `/api/profile` returns user fields directly in `data`.

---

#### PUT /api/profile

- **Auth**: Bearer Token
- **Description**: Update name, phone, or email. Email uniqueness ignores the current user.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `name` | string | Yes | max:255 |
| `email` | string | Yes | valid email, unique except self |
| `phone` | string | No | max:20 |

**Response (200):**
```json
{
  "success": true,
  "message": "Profil berhasil diupdate",
  "data": {
    "id": 1,
    "name": "Budi Santoso Updated",
    "email": "budi@example.com",
    "phone": "08123456788",
    "point_balance": 5000,
    "created_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

---

#### PUT /api/profile/password

- **Auth**: Bearer Token
- **Description**: Change password. Requires current password verification.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `current_password` | string | Yes | - |
| `new_password` | string | Yes | min:8, confirmed |
| `new_password_confirmation` | string | Yes | must match new_password |

**Response (200):**
```json
{
  "success": true,
  "message": "Password berhasil diubah",
  "data": null
}
```

**Error (422):**
```json
{
  "success": false,
  "message": "Password saat ini tidak cocok"
}
```

**Frontend Notes:**
- On success, prompt user to re-login (though the token remains valid).
- Show current password field with visibility toggle.

---

### Catalog

---

#### GET /api/categories

- **Auth**: Public
- **Description**: List active categories with product counts.

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 20 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar kategori berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Elektronik",
      "slug": "elektronik",
      "image": "http://.../category-image.jpg",
      "sort_order": 1,
      "product_count": 15
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 5 }
}
```

**Frontend Notes:**
- Use `slug` in filter parameter for products endpoint.
- `image` may be null if no image is set.
- Sorted by `sort_order` ascending.

---

#### GET /api/brands

- **Auth**: Public
- **Description**: List brands with product counts.

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 20 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar brand berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Samsung",
      "slug": "samsung",
      "product_count": 8
    }
  ],
  "meta": { ... }
}
```

**Frontend Notes:**
- Sorted alphabetically by name.
- Use `slug` in filter parameter for products endpoint.

---

#### GET /api/products

- **Auth**: Public
- **Description**: List active products with filters, sorting, and pagination. Only returns products with `is_active = 1`.

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 12 | Items per page |
| `category` | string | - | Filter by category slug |
| `brand` | string | - | Filter by brand slug |
| `gender` | integer | - | Filter: 1=Pria, 2=Wanita |
| `search` | string | - | Search name or description |
| `price_min` | integer | - | Minimum price filter |
| `price_max` | integer | - | Maximum price filter |
| `sort` | string | `newest` | Sort: `price_asc`, `price_desc`, `newest` |

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar produk berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Smartphone Galaxy S24",
      "slug": "smartphone-galaxy-s24",
      "thumbnail": "http://.../thumb.jpg",
      "category": "Elektronik",
      "brand": "Samsung",
      "gender": 1,
      "gender_label": "Pria",
      "min_price": 5000000,
      "max_price": 8000000,
      "has_active_promotion": true,
      "promo_price": 4500000,
      "average_rating": 4.5,
      "review_count": 12,
      "is_active": true
    }
  ],
  "meta": { "current_page": 1, "last_page": 5, "per_page": 12, "total": 52 }
}
```

**Frontend Notes:**
- Display `promo_price` if `has_active_promotion` is true, otherwise show `min_price`–`max_price`.
- `category` and `brand` are string names (not objects).
- Price filter checks against variant prices.
- Search is case-insensitive LIKE on name and description.
- Gender: 0=Unisex(unselected), 1=Pria, 2=Wanita.
- Inactive products are never returned.

---

### Product Detail

---

#### GET /api/products/{slug}

- **Auth**: Public
- **Description**: Get full product detail with variants, gallery images, promotions, and average rating.

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `slug` | string | Product slug (e.g., `smartphone-galaxy-s24`) |

**Response (200):**
```json
{
  "success": true,
  "message": "Detail produk berhasil diambil",
  "data": {
    "id": 1,
    "name": "Smartphone Galaxy S24",
    "slug": "smartphone-galaxy-s24",
    "description": "Deskripsi produk...",
    "features": "Fitur produk...",
    "thumbnail": "http://.../thumb.jpg",
    "category": { "id": 1, "name": "Elektronik", "slug": "elektronik", "image": null, "sort_order": 1, "product_count": 15 },
    "brand": { "id": 1, "name": "Samsung", "slug": "samsung", "product_count": 8 },
    "gender": 1,
    "gender_label": "Pria",
    "images": [
      { "id": 1, "url": "http://.../image1.jpg", "sort_order": 0 },
      { "id": 2, "url": "http://.../image2.jpg", "sort_order": 1 }
    ],
    "variants": [
      {
        "id": 1,
        "label": "128GB",
        "sku": "S24-128",
        "price": 5000000,
        "promo_price": 4500000,
        "stock": 10,
        "is_active": true
      },
      {
        "id": 2,
        "label": "256GB",
        "sku": "S24-256",
        "price": 8000000,
        "promo_price": null,
        "stock": 5,
        "is_active": true
      }
    ],
    "average_rating": 4.5,
    "total_reviews": 12,
    "is_active": true
  }
}
```

**Frontend Notes:**
- `images` sorted by `sort_order` ascending. Display as gallery.
- `variants` only returns active variants (`is_active = 1`).
- Use `promo_price` for display if non-null, otherwise use `price`.
- `stock` is the total across all warehouses.
- `features` may contain HTML or plain text.
- If product not found or inactive, returns 404.

---

#### GET /api/products/{product}/reviews

- **Auth**: Public
- **Description**: Get visible reviews for a product. Only shows reviews where `is_visible = 1`.

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `product` | integer | Product ID |

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 10 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "message": "Ulasan produk berhasil diambil",
  "data": [
    {
      "id": 1,
      "user_name": "Budi Santoso",
      "rating": 5,
      "review": "Produk bagus sekali!",
      "created_at": "2 days ago"
    }
  ],
  "meta": { ... }
}
```

**Frontend Notes:**
- `created_at` is a human-readable relative time string (e.g., "2 days ago", "3 months ago").
- `review` maps to the `reason` field in the database (the text content).
- Reviews are sorted newest first.
- Only admin-visible reviews are shown (pending reviews are hidden from public).
- 404 if product is inactive.

---

### Cart

All cart endpoints require authentication.

---

#### GET /api/cart

- **Auth**: Bearer Token
- **Description**: Get all items in the authenticated user's shopping cart.

**Response (200):**
```json
{
  "success": true,
  "message": "Keranjang berhasil diambil",
  "data": [
    {
      "id": 1,
      "product_variant_id": 1,
      "product_name": "Smartphone Galaxy S24",
      "variant_label": "128GB",
      "product_slug": "smartphone-galaxy-s24",
      "product_thumbnail": "http://.../thumb.jpg",
      "unit_price": 5000000,
      "promo_price": 4500000,
      "current_price": 4500000,
      "quantity": 2,
      "subtotal": 9000000,
      "stock_available": 10,
      "is_stock_sufficient": true
    }
  ]
}
```

**Frontend Notes:**
- Empty cart returns `{ "data": [] }` (empty array, not null).
- `current_price` reflects promo price if active, otherwise `unit_price`.
- `subtotal` = `current_price * quantity`.
- `stock_available` is total across all warehouses.
- `is_stock_sufficient` indicates if requested qty is available.
- Empty cart returns empty array `data: []`.

---

#### POST /api/cart/items

- **Auth**: Bearer Token
- **Description**: Add a product variant to cart. If the variant already exists, **quantity is incremented**, not replaced.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `product_variant_id` | integer | Yes | must exist in product_variants |
| `quantity` | integer | Yes | min:1, max:100 |

**Response (201 Created):** Returns the cart item (same structure as GET).

**Error Responses (422):**
```json
{ "message": "Varian produk tidak aktif" }
{ "message": "Stok tidak mencukupi" }
{ "message": "Varian produk tidak ditemukan" }
```

**Frontend Notes:**
- If user already has the same variant in cart, quantity increases by the sent amount (not replaces).
- Validate stock before sending request (check `stock_available` from cart or product detail).
- Max quantity per request is 100.

---

#### PUT /api/cart/items/{item}

- **Auth**: Bearer Token
- **Description**: Update quantity of a specific cart item. **Ownership check** — users can only update their own items.

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `item` | integer | Cart item ID |

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `quantity` | integer | Yes | min:1, max:100 |

**Response (200):** Returns the updated cart item.

**Error Responses:**
- `404` — Item not found (or belongs to another user)
- `422` — `"Stok tidak mencukupi"`

**Frontend Notes:**
- This sets the absolute quantity (not incremental).
- Show real-time stock validation as user types.

---

#### DELETE /api/cart/items/{item}

- **Auth**: Bearer Token
- **Description**: Remove a cart item. **Ownership check.**

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `item` | integer | Cart item ID |

**Response (200):**
```json
{
  "success": true,
  "message": "Item berhasil dihapus dari keranjang",
  "data": null
}
```

**Error Responses:**
- `404` — Item not found (or belongs to another user)

---

### Checkout

---

#### POST /api/checkout

- **Auth**: Bearer Token
- **Description**: Creates an order from the current cart contents. **Critical transaction** — uses database locking to prevent race conditions.

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `shipping_address` | string | Yes | max:500 |
| `shipping_note` | string | No | max:500 |
| `buyer_name` | string | No | max:255 (defaults to user name) |
| `buyer_email` | string | No | valid email (defaults to user email) |
| `buyer_phone` | string | No | max:20 (defaults to user phone) |
| `redeem_points` | integer | No | min:0 |

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Checkout berhasil",
  "data": {
    "id": 1,
    "order_number": "INV/20250101/00001",
    "status": 1,
    "status_label": "Menunggu Pembayaran",
    "subtotal": 9000000,
    "shipping_cost": 15000,
    "point_redeemed": 10000,
    "point_earned": 0,
    "total": 9005000,
    "items_count": 2,
    "payment_status": null,
    "payment_status_label": "Belum Dibayar",
    "created_at": "2025-01-01T12:00:00"
  }
}
```

**Error Responses (422):**
```json
{ "message": "Keranjang belanja kosong" }
{ "message": "Stok {variant_label} tidak mencukupi" }
{ "message": "Variant {variant_label} tidak aktif" }
```

**Frontend Notes:**
- **Critical**: This endpoint uses `lockForUpdate()` on stock and points tables to prevent race conditions.
- Cart is **automatically cleared** on successful checkout.
- `order_number` format: `INV/YYYYMMDD/XXXXX` (sequential per day).
- `shipping_cost` is fixed at 15,000.
- Points are deducted immediately on checkout (not on payment).
- `point_earned` is 0 initially — points are credited when admin marks as delivered.
- Show loading state — response may take time due to transaction.

**Frontend Flow:**
1. User reviews cart and enters shipping address.
2. User optionally enters points to redeem.
3. Frontend shows price breakdown: subtotal + shipping - points = total.
4. On success, clear cart state and redirect to order detail page.
5. On stock error, show which variant is out of stock.

---

### Payment

---

#### GET /api/payment-accounts

- **Auth**: Bearer Token
- **Description**: Get active bank accounts for manual transfer.

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar rekening pembayaran berhasil diambil",
  "data": [
    {
      "id": 1,
      "bank_name": "BCA",
      "account_number": "1234567890",
      "account_name": "PT EssenseLuxe"
    }
  ]
}
```

**Frontend Notes:**
- Display these accounts as transfer destination info on the payment page.
- Only active accounts are returned.

---

#### POST /api/orders/{order}/payment

- **Auth**: Bearer Token
- **Description**: Upload payment proof image for a pending order. **Ownership check.**

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `order` | integer | Order ID |

**Request Body (multipart/form-data):**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `proof` | file | Yes | image, jpg/jpeg/png, max 2MB |
| `amount` | numeric | Yes | must match order total |

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diupload",
  "data": {
    "id": 1,
    "amount": 9005000,
    "status": 1,
    "status_label": "Menunggu Konfirmasi",
    "rejected_reason": null,
    "proof_url": "uploads/payments/123_abc.jpg",
    "created_at": "2025-01-01T12:00:00"
  }
}
```

**Error Responses:**
- `404` — Order not found (or belongs to another user)
- `422` — `"Pesanan sudah tidak bisa dibayar"` (order is not pending)
- `422` — `"Sudah ada pembayaran yang menunggu konfirmasi"` (duplicate)
- `422` — `"Jumlah pembayaran harus sama dengan total pesanan"`

**Frontend Notes:**
- Upload via `multipart/form-data` (not JSON).
- Show image preview before upload.
- Amount field must exactly match order total.
- Only one pending payment per order is allowed.
- After upload, show "Menunggu Konfirmasi" status.
- Payment approval/rejection is done by admin — not handled on frontend.
- `proof_url` is a relative path, prepend the storage base URL.

---

### Orders

---

#### GET /api/orders

- **Auth**: Bearer Token
- **Description**: List authenticated user's orders with optional status filter.

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `status` | integer | - | Filter: 1=Pending, 2=Processing, 3=Shipped, 4=Delivered, 5=Cancelled |
| `sort` | string | `created_at` | Sort field |
| `order` | string | `desc` | Sort direction: `asc` or `desc` |
| `per_page` | integer | 10 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "message": "Daftar pesanan berhasil diambil",
  "data": [
    {
      "id": 1,
      "order_number": "INV/20250101/00001",
      "status": 1,
      "status_label": "Menunggu Pembayaran",
      "subtotal": 9000000,
      "shipping_cost": 15000,
      "point_redeemed": 0,
      "point_earned": 0,
      "total": 9015000,
      "items_count": 2,
      "payment_status": 1,
      "payment_status_label": "Menunggu Konfirmasi",
      "created_at": "2025-01-01T12:00:00"
    }
  ],
  "meta": { ... }
}
```

**Status Label Mapping:**
| Status | Label |
|--------|-------|
| 1 | Menunggu Pembayaran |
| 2 | Diproses |
| 3 | Dikirim |
| 4 | Selesai |
| 5 | Dibatalkan |

**Payment Status Mapping:**
| Status | Label |
|--------|-------|
| null | Belum Dibayar |
| 1 | Menunggu Konfirmasi |
| 2 | Lunas |
| 3 | Ditolak |

**Frontend Notes:**
- Use `items_count` for badge display (sum of quantities, not count of items).
- `payment_status_label` = "Belum Dibayar" when no payment exists yet.
- Tab filter by status is recommended.

---

#### GET /api/orders/{order_number}

- **Auth**: Bearer Token
- **Description**: Get full order detail including items, payment history, and shipment tracking.

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `order_number` | string | Order number (e.g. `INV/20250101/00001`) |

**Response (200):**
```json
{
  "success": true,
  "message": "Detail pesanan berhasil diambil",
  "data": {
    "id": 1,
    "order_number": "INV/20250101/00001",
    "status": 1,
    "status_label": "Menunggu Pembayaran",
    "buyer_name": "Budi Santoso",
    "buyer_email": "budi@example.com",
    "buyer_phone": "08123456789",
    "shipping_address": "Jl. Merdeka No. 1, Jakarta",
    "shipping_note": "Pagi hari",
    "subtotal": 9000000,
    "shipping_cost": 15000,
    "point_redeemed": 0,
    "point_earned": 0,
    "total": 9015000,
    "paid_at": null,
    "created_at": "2025-01-01T12:00:00",
    "updated_at": "2025-01-01T12:00:00",
    "items": [
      {
        "id": 1,
        "product_name": "Smartphone Galaxy S24",
        "variant_label": "128GB",
        "unit_price": 4500000,
        "quantity": 2,
        "subtotal": 9000000
      }
    ],
    "payments": [
      {
        "id": 1,
        "amount": 9015000,
        "status": 1,
        "status_label": "Menunggu Konfirmasi",
        "rejected_reason": null,
        "proof_url": "uploads/payments/123_abc.jpg",
        "bank_name": "BCA",
        "account_number": "1234567890",
        "account_name": "PT EssenseLuxe",
        "created_at": "2025-01-01T12:30:00"
      }
    ],
    "shipment": {
      "id": 1,
      "status": 4,
      "status_label": "Selesai",
      "courier_name": "JNE",
      "tracking_number": "JNE123456789",
      "shipping_cost": 15000,
      "delivered_at": "2025-01-05T14:00:00",
      "warehouse": "Gudang Utama",
      "tracking_logs": [
        {
          "status": 1,
          "status_label": "Dikemas",
          "note": "Pesanan sedang dikemas",
          "location": "Jakarta",
          "created_at": "2025-01-02T09:00:00"
        },
        {
          "status": 2,
          "status_label": "Dikirim",
          "note": "Pesanan telah dikirim",
          "location": "Jakarta",
          "created_at": "2025-01-02T15:00:00"
        }
      ]
    }
  }
}
```

**Frontend Notes:**
- `shipment` is `null` if order has not shipped yet.
- `payments` array may have 0 or more entries.
- `rejected_reason` is only present when payment status = 3 (Ditolak).
- Items use snapshot data (product_name, variant_label are copied at order time).
- Order is looked up by `order_number` format: `INV/YYYYMMDD/XXXXX`.
- Returns 404 if order belongs to another user.

---

### Cancel Order

---

#### POST /api/orders/{order}/cancel

- **Auth**: Bearer Token
- **Description**: Cancel a pending or processing order. **Ownership check.** Restores stock and redeemed points.

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `order` | integer | Order ID |

**Response (200):**
```json
{
  "success": true,
  "message": "Pesanan berhasil dibatalkan",
  "data": { ...order detail with status_label: "Dibatalkan" }
}
```

**Error Responses:**
- `404` — Order not found (or belongs to another user)
- `422` — `"Pesanan tidak dapat dibatalkan"` (shipped/delivered orders cannot be cancelled)

**Frontend Notes:**
- Show cancel button only when `status` is 1 (Pending) or 2 (Processing).
- On cancel, stock is restored and redeemed points are returned.
- Add confirmation dialog before cancelling.
- After cancel, refresh order list and show "Dibatalkan" status.

---

### Shipment Tracking

---

#### GET /api/orders/{order}/tracking

- **Auth**: Bearer Token
- **Description**: Get shipment tracking information for an order. **Read-only.**

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `order` | integer | Order ID |

**Response (200):**
```json
{
  "success": true,
  "message": "Data pengiriman berhasil diambil",
  "data": {
    "shipment": {
      "id": 1,
      "status": 3,
      "status_label": "Dalam Perjalanan",
      "courier_name": "JNE",
      "tracking_number": "JNE123456789",
      "shipping_cost": 15000,
      "warehouse": "Gudang Utama",
      "delivered_at": null
    },
    "tracking_logs": [
      {
        "id": 1,
        "status": 1,
        "status_label": "Dikemas",
        "note": "Pesanan sedang dikemas",
        "location": "Jakarta",
        "created_at": "2025-01-02T09:00:00"
      }
    ]
  }
}
```

**Shipment Status Mapping:**
| Status | Label |
|--------|-------|
| 1 | Dikemas |
| 2 | Dikirim |
| 3 | Dalam Perjalanan |
| 4 | Selesai |

**Frontend Notes:**
- `shipment` is `null` and `tracking_logs` is `[]` if no shipment exists yet.
- Tracking logs are sorted by `created_at` ascending (oldest first).
- Display as a timeline/stepper UI.
- This is read-only — no update functionality exists.
- `warehouse` is the warehouse name string.

---

### Reward Points

---

#### GET /api/rewards/balance

- **Auth**: Bearer Token
- **Description**: Get loyalty points balance. Returns 0 for new users.

**Response (200):**
```json
{
  "success": true,
  "message": "Saldo poin berhasil diambil",
  "data": {
    "balance": 5000
  }
}
```

**Frontend Notes:**
- Always returns a value (creates record if not exists).
- Display in navbar or profile section.
- Points are earned when orders are delivered (admin workflow).

---

#### GET /api/rewards/transactions

- **Auth**: Bearer Token
- **Description**: Get paginated history of point transactions.

**Query Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 15 | Items per page |

**Response (200):**
```json
{
  "success": true,
  "message": "Riwayat poin berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "type": 1,
        "type_label": "Poin Masuk",
        "amount": 5000,
        "description": "Poin dari pesanan INV/20250101/00001",
        "order_number": "INV/20250101/00001",
        "created_at": "2025-01-05T14:00:00"
      },
      {
        "id": 2,
        "type": 2,
        "type_label": "Poin Keluar",
        "amount": 1000,
        "description": "Redeem poin untuk pesanan",
        "order_number": "INV/20250101/00002",
        "created_at": "2025-01-06T10:00:00"
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 2
  }
}
```

**Point Type Mapping:**
| Type | Label |
|------|-------|
| 1 | Poin Masuk (earned) |
| 2 | Poin Keluar (redeemed) |

**Frontend Notes:**
- Transaction list is inside `data.data` (double-wrapped due to paginated resource).
- `order_number` may be null for non-order transactions.
- `type` = 1 for earned (after delivered), 2 for redeemed (at checkout).
- Sorted newest first.

---

### Review Submission

---

#### POST /api/orders/{order}/reviews

- **Auth**: Bearer Token
- **Description**: Submit a product review for a delivered order item. **Ownership check.**

**URL Parameters:**
| Param | Type | Description |
|-------|------|-------------|
| `order` | integer | Order ID |

**Request Body:**
| Field | Type | Required | Rules |
|-------|------|:--------:|-------|
| `product_variant_id` | integer | Yes | must exist in product_variants |
| `rating` | integer | Yes | min:1, max:5 |
| `review` | string | No | max:1000 |

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Ulasan berhasil dikirim",
  "data": {
    "id": 1,
    "user_name": "Budi Santoso",
    "rating": 5,
    "review": "Produk bagus sekali!",
    "created_at": "2 days ago"
  }
}
```

**Error Responses:**
- `404` — Order not found (or belongs to another user)
- `422` — `"Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai"` (not delivered)
- `422` — `"Varian produk tidak ditemukan di pesanan ini"` (variant not in order)
- `422` — `"Anda sudah memberikan ulasan untuk produk ini"` (duplicate)

**Frontend Notes:**
- Show review form only for orders with `status = 4` (Delivered).
- One review per product variant per order.
- Rating is 1-5 (star selector).
- Review is optional.
- On submit success, the review is immediately visible (`is_visible = true`), but admin can toggle visibility.
- Use GET `/api/products/{product}/reviews` to display reviews on the product page.

---

## 5. Frontend Flow Guide

### A. Guest Flow

1. **Homepage/Browse**: Fetch categories, brands, and products simultaneously.
2. **Category/Brand Selection**: Use `?category=slug` or `?brand=slug`.
3. **Search**: Debounced search with `?search=term`.
4. **Price Filter**: `?price_min=10000&price_max=500000`.
5. **Sort**: `?sort=price_asc`, `?sort=price_desc`, `?sort=newest`.
6. **Product Detail**: Click product → navigate to `/products/{slug}`.
7. **Reviews**: Scroll to review section on product detail page.

### B. Auth Flow

1. **Register**: Collect name, email, phone, password → `POST /api/register`.
2. **Login**: Collect email, password → `POST /api/login`.
3. **Store Token**: Save to Alpine.js `$store.auth` + localStorage.
4. **Attach Header**: Axios interceptor adds `Authorization: Bearer {token}`.
5. **Hydrate**: On app load, call `GET /api/me` to restore session.
6. **Logout**: Call `POST /api/logout` → clear token → redirect to login.

### C. Shopping Flow

1. **View Cart**: `GET /api/cart` → display items with thumbnail, name, variant, price, qty, subtotal.
2. **Add to Cart**: From product detail page → select variant → `POST /api/cart/items`.
3. **Update Quantity**: In cart page → `PUT /api/cart/items/{item}`.
4. **Remove Item**: Delete button → `DELETE /api/cart/items/{item}`.
5. **Price Display**: Show `current_price` (promo price if active, otherwise unit price).
6. **Stock Warning**: Highlight items where `is_stock_sufficient = false`.
7. **Subtotal**: Calculate from `current_price * quantity` for each item.

### D. Checkout Flow

1. User reviews cart items and prices.
2. User optionally enters redeem points amount.
3. User fills shipping address (required) and optional shipping note.
4. Frontend shows price breakdown: subtotal + shipping - points = total.
5. `POST /api/checkout` with shipping data and optional `redeem_points`.
6. **On success**: Clear cart UI, redirect to order detail page with `order_number`.
7. **On stock error**: Show which variant is out of stock → allow user to adjust cart.
8. **Loading state**: Transaction may take a few seconds — show spinner.

### E. Payment Flow

1. On order detail page with `status = 1` (Menunggu Pembayaran):
   - Show payment accounts (`GET /api/payment-accounts`).
   - Show "Upload Bukti Transfer" form.
2. User uploads proof image and enters transfer amount.
3. `POST /api/orders/{order}/payment` with `multipart/form-data`.
4. **On success**: Show "Menunggu Konfirmasi" status.
5. **On error**: Show error message (e.g., amount mismatch, duplicate payment).

### F. Order Flow

1. **Order List Page**: `GET /api/orders` with optional `?status=1` filter.
2. **Status Tabs**: Pending (1), Processing (2), Shipped (3), Delivered (4), Cancelled (5).
3. **Order Detail**: Click order → `GET /api/orders/{order_number}`.
4. Show payment proof, shipment timeline if available.
5. Show "Upload Payment" button if status is Pending.
6. Show "Cancel" button if status is Pending or Processing.
7. Show "Review" button if status is Delivered.
8. Show tracking info if shipment exists.

### G. Shipment Flow

1. On order detail page, if `shipment` is not null, show tracking section.
2. Timeline UI: Display `tracking_logs` in chronological order.
3. Each log shows: status, note, location, timestamp.
4. No user action needed — read-only.

### H. Reward Flow

1. **Navbar/Header**: Show point balance from `GET /api/rewards/balance`.
2. **Reward Page**: `GET /api/rewards/transactions` with paginated history.
3. Display type (earned/redeemed), amount, description, order number, date.
4. Points can be used at checkout (redeem).

### I. Review Flow

1. On delivered order detail page, show "Beri Ulasan" for each order item.
2. Only show for `status = 4` (Delivered).
3. Star selector (1-5 required) + optional text review.
4. `POST /api/orders/{order}/reviews`.
5. On success, review immediately appears on product page (if admin keeps it visible).
6. Each product variant in an order can only be reviewed once.

### J. Profile Flow

1. **View Profile**: `GET /api/profile` → show name, email, phone, join date.
2. **Edit Profile**: `PUT /api/profile` → update name, email, phone.
3. **Change Password**: `PUT /api/profile/password` → current + new + confirm.

---

## 6. Frontend State Management Recommendation

### Alpine.js Store Structure

Use Alpine.js `Alpine.store()` for global state management. Define stores in a global JavaScript file loaded in your layout.

```javascript
// resources/js/stores.js — loaded globally in your Blade layout
import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
  // Auth Store
  Alpine.store('auth', {
    user: null,
    token: localStorage.getItem('auth_token') || null,

    get isAuthenticated() {
      return !!this.token;
    },

    setAuth(user, token) {
      this.user = user;
      this.token = token;
      localStorage.setItem('auth_token', token);
    },

    clearAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('auth_token');
    },
  });

  // Cart Store
  Alpine.store('cart', {
    items: [],
    isLoading: false,

    get totalItemCount() {
      return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    get subtotal() {
      return this.items.reduce((sum, item) => sum + item.subtotal, 0);
    },

    setItems(items) {
      this.items = items;
    },

    addItem(item) {
      const existing = this.items.find(i => i.product_variant_id === item.product_variant_id);
      if (existing) {
        existing.quantity = item.quantity;
        existing.subtotal = item.subtotal;
      } else {
        this.items.push(item);
      }
    },

    updateItem(itemId, quantity, subtotal) {
      const item = this.items.find(i => i.id === itemId);
      if (item) {
        item.quantity = quantity;
        item.subtotal = subtotal;
      }
    },

    removeItem(itemId) {
      this.items = this.items.filter(i => i.id !== itemId);
    },

    clear() {
      this.items = [];
    },
  });
});
```

### Using Stores in Blade Views

```blade
{{-- Example: Navbar showing auth state and cart count --}}
<nav x-data>
  <template x-if="$store.auth.isAuthenticated">
    <div>
      <span x-text="$store.auth.user?.name"></span>
      <a href="/cart">Cart (<span x-text="$store.cart.totalItemCount"></span>)</a>
      <button @click="logout">Logout</button>
    </div>
  </template>
  <template x-if="!$store.auth.isAuthenticated">
    <div>
      <a href="/login">Login</a>
      <a href="/register">Register</a>
    </div>
  </template>
</nav>
```

### Data Shape Reference (JSON structure your JS will consume)

Use these shapes to guide your Axios response handling and Alpine.js store updates.

**User:**
```json
{ "id": 1, "name": "Budi Santoso", "email": "budi@example.com", "phone": "08123456789", "point_balance": 5000, "created_at": "2025-01-01T00:00:00.000000Z" }
```

**Category:** `{ "id": 1, "name": "Elektronik", "slug": "elektronik", "image": null, "sort_order": 1, "product_count": 15 }`

**Brand:** `{ "id": 1, "name": "Samsung", "slug": "samsung", "product_count": 8 }`

**Product (list):** `{ "id": 1, "name": "...", "slug": "...", "thumbnail": null, "category": "Elektronik", "brand": "Samsung", "gender": 1, "gender_label": "Pria", "min_price": 5000000, "max_price": 8000000, "has_active_promotion": true, "promo_price": 4500000, "average_rating": 4.5, "review_count": 12, "is_active": true }`

**ProductVariant:** `{ "id": 1, "label": "128GB", "sku": "S24-128", "price": 5000000, "promo_price": 4500000, "stock": 10, "is_active": true }`

**CartItem:** `{ "id": 1, "product_variant_id": 1, "product_name": "...", "variant_label": "128GB", "product_slug": "...", "product_thumbnail": null, "unit_price": 5000000, "promo_price": 4500000, "current_price": 4500000, "quantity": 2, "subtotal": 9000000, "stock_available": 10, "is_stock_sufficient": true }`

**Order (summary):** `{ "id": 1, "order_number": "INV/20250101/00001", "status": 1, "status_label": "Menunggu Pembayaran", "subtotal": 9000000, "shipping_cost": 15000, "point_redeemed": 0, "point_earned": 0, "total": 9015000, "items_count": 2, "payment_status": null, "payment_status_label": "Belum Dibayar", "created_at": "..." }`

**OrderItem:** `{ "id": 1, "product_name": "...", "variant_label": "128GB", "unit_price": 4500000, "quantity": 2, "subtotal": 9000000 }`

**Payment:** `{ "id": 1, "amount": 9015000, "status": 1, "status_label": "Menunggu Konfirmasi", "rejected_reason": null, "proof_url": "uploads/payments/...", "bank_name": "BCA", "account_number": "1234567890", "account_name": "PT EssenseLuxe", "created_at": "..." }`

**Shipment + TrackingLogs:** See section 4 endpoint docs for full nested structure.

**RewardTransaction:** `{ "id": 1, "type": 1, "type_label": "Poin Masuk", "amount": 5000, "description": "...", "order_number": "INV/...", "created_at": "..." }`

**Review:** `{ "id": 1, "user_name": "Budi Santoso", "rating": 5, "review": "Produk bagus sekali!", "created_at": "2 days ago" }`

**Paginated Response Structure:**
```json
{
  "data": [ ... ],
  "meta": { "current_page": 1, "last_page": 5, "per_page": 12, "total": 52 }
}
```

**API Envelope Structure:**
```json
{
  "success": true,
  "message": "Operation message",
  "data": { ... }
}
```

---

## 7. Axios/Fetch Integration Guide

### Blade Layout Setup

Pass the API base URL from Laravel `.env` to your Blade layout:

```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-base-url" content="{{ config('app.url') }}/api">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{ $slot }}
</body>
</html>
```

### Laravel .env

```env
APP_URL=http://127.0.0.1:8000
```

### Axios Client Setup

```javascript
// resources/js/api.js
import axios from 'axios';

const apiClient = axios.create({
  baseURL: document.querySelector('meta[name="api-base-url"]')?.content || 'http://127.0.0.1:8000/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

// Request interceptor — attach Bearer token
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor — handle 401
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

### Alpine.js + Axios Integration in Blade

```javascript
// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';
import apiClient from './api';
import './stores'; // your Alpine stores

window.apiClient = apiClient;
window.Alpine = Alpine;

Alpine.start();
```

### API Call Examples

```javascript
// ===== LOGIN (called from Alpine component) =====
const login = async (email, password) => {
  try {
    const response = await apiClient.post('/login', { email, password });
    const { token, user } = response.data.data;
    localStorage.setItem('auth_token', token);
    Alpine.store('auth').setAuth(user, token);
    window.location.href = '/';
  } catch (error) {
    const message = error.response?.data?.message || 'Login gagal';
    alert(message);
  }
};

// ===== GET PRODUCTS (with filters) =====
const getProducts = async (filters = {}) => {
  const params = new URLSearchParams();
  Object.entries(filters).forEach(([key, value]) => {
    if (value !== undefined && value !== '' && value !== null) {
      params.append(key, String(value));
    }
  });
  const response = await apiClient.get(`/products?${params.toString()}`);
  return response.data; // { data: [...], meta: {...} }
};

// ===== ADD TO CART =====
const addToCart = async (productVariantId, quantity) => {
  const response = await apiClient.post('/cart/items', {
    product_variant_id: productVariantId,
    quantity,
  });
  return response.data.data; // CartItem object
};

// ===== CHECKOUT =====
const checkout = async (checkoutData) => {
  const response = await apiClient.post('/checkout', checkoutData);
  return response.data.data; // OrderSummary object
};

// ===== UPLOAD PAYMENT PROOF =====
const uploadPaymentProof = async (orderId, file, amount) => {
  const formData = new FormData();
  formData.append('proof', file);
  formData.append('amount', String(amount));

  const response = await apiClient.post(`/orders/${orderId}/payment`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data; // Payment object
};
```

### Example: Alpine Component for Login

```blade
{{-- resources/views/auth/login.blade.php --}}
<div x-data="loginForm()">
  <form @submit.prevent="submit">
    <div>
      <label>Email</label>
      <input type="email" x-model="form.email">
      <template x-if="errors.email">
        <p class="text-red-500" x-text="errors.email[0]"></p>
      </template>
    </div>
    <div>
      <label>Password</label>
      <input type="password" x-model="form.password">
    </div>
    <button type="submit" x-text="isLoading ? 'Loading...' : 'Login'"></button>
  </form>
</div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
      form: { email: '', password: '' },
      errors: {},
      isLoading: false,

      async submit() {
        this.isLoading = true;
        this.errors = {};
        try {
          const res = await apiClient.post('/login', this.form);
          const { token, user } = res.data.data;
          Alpine.store('auth').setAuth(user, token);
          window.location.href = '/';
        } catch (err) {
          if (err.response?.status === 422) {
            this.errors = err.response.data.errors || {};
          } else {
            alert(err.response?.data?.message || 'Terjadi kesalahan');
          }
        } finally {
          this.isLoading = false;
        }
      },
    }));
  });
</script>
```

---

## 8. Important Business Rules for FE

| Rule | Description |
|------|-------------|
| **Public Endpoints** | `GET /api/categories`, `GET /api/brands`, `GET /api/products`, `GET /api/products/{slug}`, `GET /api/products/{product}/reviews` do not need Bearer token. |
| **Protected Endpoints** | All other endpoints require `Authorization: Bearer {token}` header. |
| **Ownership Scoping** | A customer can only access their own cart, orders, reviews, rewards, and profile. Accessing another user's resource returns **404** (not 403). |
| **Cart Uniqueness** | Cart items are unique by `user_id + product_variant_id`. Adding the same variant increments quantity, not duplicates. |
| **Current Price** | The price shown may differ from `unit_price` if an active promotion applies to the variant. Always display `current_price`. |
| **Stock Volatility** | Stock availability shown in cart is a snapshot. Actual stock is validated at checkout time. |
| **Checkout Failure** | Checkout may fail if stock changed between adding to cart and checkout. Handle these errors gracefully. |
| **Payment Upload** | Payment proof can only be uploaded when order status is 1 (Pending). Only one pending payment per order is allowed. |
| **Payment Approval** | Payment approval or rejection is done by admin. The frontend cannot change payment status. |
| **Cancel Eligibility** | Order cancellation is only allowed for status 1 (Pending) or 2 (Processing). Shipped and delivered orders cannot be cancelled. |
| **Tracking Read-Only** | Shipment tracking is read-only. Users can only view tracking logs. |
| **Point Credit** | Reward points are credited to the user's account when admin marks the order as delivered. |
| **Points at Checkout** | Points redeemed at checkout are deducted immediately, not when payment is confirmed. |
| **Review Eligibility** | Reviews can only be submitted for orders with status 4 (Delivered). One review per variant per order. |
| **Review Visibility** | Reviews are created with `is_visible = true` by default, but admin can toggle visibility. The frontend should inform users that reviews may be moderated. |
| **Soft-Deleted Users** | Soft-deleted users cannot login and get `"Akun telah dinonaktifkan."` |
| **Login Revokes Tokens** | Every login call revokes ALL previous tokens for that user. Store only the latest token. |

---

## 9. Error Handling Guide

| HTTP Status | Meaning | Frontend Action |
|:-----------:|---------|-----------------|
| **401** | Unauthenticated — token missing, expired, or revoked. | Clear token from storage. Redirect to `/login`. Show toast: "Sesi telah berakhir, silakan login kembali". |
| **403** | Forbidden — not used in this API (ownership errors return 404 instead). | N/A |
| **404** | Not found — resource doesn't exist or belongs to another user. | Show "Tidak ditemukan" page. Or redirect user back to safe page. |
| **422** | Validation error — invalid input data. | Show validation errors inline next to each field. Parse `errors` object for field-specific messages. For business rule errors (stock, payment), show the message in a toast/alert. |
| **409** | Conflict — not used in this API (stock conflicts return 422). | N/A |
| **429** | Too many requests — rate limited (if enabled). | Show "Terlalu banyak permintaan, coba lagi nanti". |
| **500** | Server error — unexpected backend failure. | Show generic error message: "Terjadi kesalahan server, silakan coba lagi". Log details for debugging. |

### Error Handling Pattern

```javascript
// resources/js/utils.js
export function handleApiError(error) {
  if (error.response) {
    const { status, data } = error.response;

    switch (status) {
      case 401:
        return 'Sesi telah berakhir, silakan login kembali';
      case 422:
        // Return first validation error, or business rule error
        if (data.errors) {
          const firstField = Object.keys(data.errors)[0];
          return data.errors[firstField][0];
        }
        return data.message || 'Data yang dimasukkan tidak valid';
      case 404:
        return 'Data tidak ditemukan';
      default:
        return 'Terjadi kesalahan, silakan coba lagi';
    }
  }

  return 'Koneksi gagal, periksa koneksi internet Anda';
}
```

### Usage in Alpine Component

```javascript
import { handleApiError } from './utils';

// Inside your Alpine data component:
async addToCart(variantId, qty) {
  try {
    const item = await apiClient.post('/cart/items', {
      product_variant_id: variantId,
      quantity: qty,
    });
    Alpine.store('cart').addItem(item.data.data);
  } catch (error) {
    this.errorMessage = handleApiError(error);
  }
}
```

---

## 10. Deliverable Summary

### File Created
**`FRONTEND_API_HANDOFF.md`** — Complete frontend developer handoff documentation.

### Endpoints Documented
- **Total endpoints**: 27
- **Implemented**: 27 (100%)
- **Planned / Not Implemented**: 0
- **Public endpoints**: 5 (register, login, categories, brands, products + detail + reviews)
- **Protected endpoints**: 22 (logout, me, profile, cart, checkout, payment, orders, tracking, rewards, reviews)

### Main Frontend Integration Notes

1. **Authentication**: Sanctum Bearer token. Register and login return the token. Store in Alpine.js `$store.auth` + localStorage. Attach via Axios interceptor. On 401, auto-redirect to login.

2. **Standard Response Envelope**: Every response has `{ success, message, data }`. List endpoints also include `meta` for pagination. Error responses include `message` (and optionally `errors` for validation).

3. **Ownership via 404**: Accessing another user's data returns 404, not 403. Handle accordingly in the UI.

4. **Price Display**: Always use `current_price` (or `promo_price` if available) instead of `unit_price`. Products list shows range `min_price`–`max_price` unless a promotion sets a single `promo_price`.

5. **Checkout is Critical**: Uses database transactions with row locking. May take time. Show loading state. Stock errors can happen — redirect user to adjust cart.

6. **Cart is Incremental**: Adding the same variant increments qty, not duplicates. Quantity update is absolute (sets to the sent value).

7. **Order Number Format**: `INV/YYYYMMDD/XXXXX` — use this for order detail URL parameter.

8. **Payment Upload**: Must use `multipart/form-data`. Image max 2MB, jpg/jpeg/png only. Amount must exactly match order total.

9. **Review is Optional Text**: The `review` field maps to `reason` in the database. Frontend sends as `review`, API stores as `reason`.

10. **Points**: Balance shows on profile and me endpoints. History has double-wrapped `data.data` due to paginated resource.

11. **No Admin Functionality**: This API serves customers only. Admin operations (payment approval, order processing, product management) are in a separate admin panel.

12. **Cancellation Restores**: When an order is cancelled, stock and redeemed points are automatically restored.
