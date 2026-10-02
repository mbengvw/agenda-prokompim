<sim-pimpinan-architecture-and-design-rules>
# SIM Pimpinan Architecture and Design Rules

## 1. Clean Code Architecture
- We always use the clean code pattern: `Controller -> Service -> Repository` for CREATE and UPDATE operations.
- For READ operations, we use the pattern: `Controller -> QueryService`.
- Use the existing `app/Services`, `app/Repositories`, and `app/QueryServices` directories.

## 2. UI Design
- All UI design MUST be **Mobile First**, as the majority of users will be accessing the application via mobile devices.
</sim-pimpinan-architecture-and-design-rules>
