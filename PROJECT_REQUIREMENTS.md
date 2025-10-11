# E-Payroll System - Project Requirements

## System Overview

This is a payment collection system for a Company that manages construction workers. Clients (contractors/employers) pay workers' salaries through this system to the Company's bank account. The Company then uses their own separate payroll system to pay the actual workers.

## System Entities

### 1. Clients/Employers/Contractors (Many)
- Companies that hire construction workers
- Pay for workers' services through this e-payroll system
- Payments go to the Company's bank account
- Multiple clients can exist in the system

### 2. The Company (One)
- Single company that manages all construction workers
- Acts as intermediary/payment collector
- Receives all payments from clients into their bank account
- Uses a separate real payroll system (outside this e-payroll) to actually pay workers

### 3. Construction Workers (Many)
- Contract-based workers (not permanent employees)
- Work on construction projects for various clients
- Managed by the Company
- Get paid by the Company through a different system
- **NO ACCESS to this e-payroll system**

## Process Flow

1. Client uses this e-payroll system to submit payroll data (hours worked, amounts, etc.)
2. Client makes payment through the system
3. Money goes to Company's bank account
4. Company receives the funds
5. Company uses their own real payroll system (separate) to pay actual workers

## Technical Requirements

### Database
- **Type:** MySQL
- **Two databases:**
  - Primary: This e-payroll system database
  - Secondary: Existing database with worker biodata (will be linked)
- **Access:** This system will READ and UPDATE worker data from secondary database

### Payment Integration
- **Gateway:** Billplz (Malaysian payment gateway)
- **Supported methods:** FPX, credit/debit cards, e-wallets
- **Recipient:** All payments to Company's bank account
- **Approval:** No approval needed - direct payment

### User Roles & Access

1. **Company Admin**
   - Full system access
   - Manage clients
   - View all payments
   - Access worker data
   - Generate reports

2. **Clients/Contractors**
   - Submit payroll data
   - Make payments
   - View their submissions
   - Access assigned workers

3. **Workers**
   - NO ACCESS to this system
   - Data managed through secondary database

### Payroll Submission

#### Submission Type
- Flexible: per worker OR batch submission
- Cannot exceed new month
- Must be paid before new month starts

#### Pay Period
- Monthly (for basic salary)

#### Data Submitted by Clients
- Worker hours worked
- Amounts
- Other details (to be specified later)

#### Worker Biodata Source
- Retrieved from secondary database
- Includes: name, ID, position, rate, etc. (to be detailed)

#### Calculations
- Will be provided later
- May include: overtime, deductions, bonuses, etc.

## Key Features to Implement

### Core Features
1. Client dashboard
2. Payroll submission form (single worker & batch)
3. Billplz payment integration
4. Payment tracking & transaction history
5. Monthly deadline enforcement
6. Worker data management (linked to secondary DB)
7. Reports & exports

### Additional Features (TBD)
- Notifications (email/SMS)
- Payment reminders
- Audit logs
- Data analytics/reports
- Mobile responsiveness

## Development Notes

- Laravel framework
- MySQL database
- Billplz PHP SDK for payment integration
- Role-based access control (RBAC)
- Secure authentication
- Data validation & security measures

## Next Steps

1. Define detailed calculation formulas
2. Specify complete worker biodata fields from secondary database
3. Design database schema
4. Plan UI/UX flow
5. Set up Billplz integration
6. Implement core features
7. Testing & deployment

## Questions for Later

1. Detailed calculation formulas (overtime, deductions, taxes, etc.)
2. Complete list of worker data fields from secondary database
3. Notification requirements
4. Reporting requirements
5. Any approval workflows needed
6. Client registration process
7. Security & compliance requirements
