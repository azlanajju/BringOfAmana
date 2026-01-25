# Bright of Amana – Database

## Quick start

1. Start MySQL (XAMPP).
2. Run the schema:

   ```bash
   mysql -u root -p < database/schema.sql
   ```

   Or via phpMyAdmin: create DB `bright_of_amana`, then import `schema.sql`.

## Tables

| Table | Purpose |
|-------|--------|
| **users** | Admin & investor accounts (email, role, password_hash, status) |
| **investors** | Investor profiles (investor_code, join_date), links to `users` |
| **investments** | Monthly submissions (amount, proof, status, admin remark) |
| **admin_actions_log** | Audit log for approve/reject/view actions |
| **password_reset_tokens** | Forgot-password tokens |

## Notes

- **Unique constraint:** One investment per investor per month/year (`investor_id`, `month`, `year`).
- **Roles:** `super_admin`, `admin`, `staff`, `investor`.
- **Investment status:** `pending` → `approved` or `rejected`.
- Create your first admin via your app’s registration/seeding; do not use the commented seed in `schema.sql` for production.
