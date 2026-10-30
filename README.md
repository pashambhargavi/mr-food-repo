# Anandam Sweets — Laravel 11 REST API Backend & Admin Portal

This is the complete **Laravel 11** backend for Anandam Sweets, providing:
1. **Full REST API** for the Next.js Storefront (Catalogue, Weight Variants, Cart, Coupons, Orders, Tracking).
2. **Dedicated Admin Portal** (`/admin`) for shop owners to manage live orders, mark COD cash collection, update variant stock levels, edit shop settings, and print thermal 80mm receipts.

---

## 🚀 Quick Start (Local Development)

### 1-Click Launch:
Double-click [**`run-backend.bat`**](file:///d:/BHARGAVI/agency-agents/sweet-shop-backend/run-backend.bat) in File Explorer or run:
```cmd
set PATH=C:\Users\DELL\php;%PATH%
cd /d d:\BHARGAVI\agency-agents\sweet-shop-backend
php artisan serve --port=8000
```

- **Admin Portal**: [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **REST API Base**: [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)
- **Default Admin Account**: `admin@anandamsweets.in` / `admin123`

---

## 📡 REST API Reference

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/categories` | List active sweet categories |
| `GET` | `/api/products` | Sweets catalogue with weight variants & filters (`?cat=`, `?diet=`, `?q=`, `?sort=`) |
| `GET` | `/api/products/{slug}` | Single sweet detail, ingredients, and related recommendations |
| `POST` | `/api/coupons/validate` | Validates promo coupon against subtotal, returns discount paise |
| `POST` | `/api/orders` | Places order, validates prices server-side, decrements stock atomically |
| `GET` | `/api/orders/{orderNumber}` | Real-time order progress timeline and items snapshot |
| `POST` | `/api/orders/{orderNumber}/cancel` | Customer order cancellation before dispatch |
| `GET` | `/api/settings` | Store timings, address, delivery fees, and UPI VPA |

---

## 🛠️ Admin Dashboard Modules (`/admin`)

1. **Overview Dashboard** (`/admin`):
   - Today's Sales, Today's Orders, Pending Processing count.
   - Low-Stock alerts (variants with ≤ 15 units remaining).
   - Recent orders quick-ticker.

2. **Orders Pipeline** (`/admin/orders`):
   - Filter by status (`placed`, `confirmed`, `preparing`, `ready_for_dispatch`, `ready_for_pickup`, `delivered`, `cancelled`).
   - One-click status change with audit trail notes.
   - **Cash on Delivery (COD) Collection**: Mark cash collected by delivery staff.
   - **Printable 80mm Thermal Receipt**: Formatted packing slip with items, weights, and customer address.

3. **Sweets Inventory** (`/admin/products`):
   - View all sweet varieties and multilingual titles (EN, TE, HI).
   - Inline stock editor for each weight variant (250g, 500g, 1kg).

4. **Business Settings** (`/admin/settings`):
   - Update shop name, phone, WhatsApp, opening hours, delivery fees, and merchant UPI VPA.

---

## 🧪 Automated Testing

Run the included PHPUnit test suite anytime:
```cmd
set PATH=C:\Users\DELL\php;%PATH%
cd /d d:\BHARGAVI\agency-agents\sweet-shop-backend
php artisan test --filter=SweetShopApiTest
```
Tests verify categories, products with variants, coupon thresholds, atomic stock decrements, and order creation.
