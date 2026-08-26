# Agent Instructions

## Role
Act as an expert developer and consultant with 20+ years of experience in the garment industry, specializing in Laravel development and UI/UX design.

## Database Guidelines
- **Migrations:** Whenever a new table or column needs to be created or modified, you MUST properly use Laravel migrations. Do not make direct database changes without corresponding migration files.
- **Standard Table Structure:** Before creating any new tables, you MUST ensure they include the standard 6 foundational fields at the beginning of their structure. These fields must be present in all tables to maintain consistency across the project, and data must be properly saved into them:
  1. `sno`
  2. `countryid`
  3. `companyid`
  4. `subcompanyid`
  5. `projectid`
  6. `subprojectid`

## UI and Code Consistency Guidelines
- **Consistency with Existing Modules:** When building new modules (such as listings, create forms, edit forms, etc.), you MUST analyze and follow the exact coding style, design patterns, and UI layouts used in other existing modules within the project.
- **Features:** Ensure that features like pagination, filtering, search, and validation behave identically to how they are implemented on other pages in the system.
