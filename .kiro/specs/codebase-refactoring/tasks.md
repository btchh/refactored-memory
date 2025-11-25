# Implementation Plan: Codebase Refactoring

This implementation plan breaks down the refactoring into discrete, manageable tasks. Each task builds incrementally on previous tasks to transform the codebase into a clean, well-organized structure.

## Phase 1: Backend Repository Layer

- [x] 1. Create repository interfaces and base implementations





  - Create `app/Repositories/Contracts/UserRepositoryInterface.php` with all required methods
  - Create `app/Repositories/Contracts/AdminRepositoryInterface.php` with a ll required methods
  - Create `app/Repositories/UserRepository.php` implementing the interface
  - Create `app/Repositories/AdminRepository.php` implementing the interface
  - _Requirements: 1.2, 1.3_

- [x] 2. Register repositories in service provider




  - Update `app/Providers/AppServiceProvider.php` to bind repository interfaces to implementations
  - Add repository bindings in the `register()` method
  - _Requirements: 1.2_

## Phase 2: Backend Service Layer Refactoring

- [x] 3. Create authentication services





  - Create `app/Services/Auth/AdminAuthService.php` with login, logout, loginById methods
  - Create `app/Services/Auth/UserAuthService.php` with login, logout, loginById methods
  - Extract authentication logic from existing `AuthService` into new services
  - Update services to use repository pattern for data access
  - _Requirements: 1.1, 1.2, 8.1, 8.5_

- [x] 4. Create OTP service









  - Create `app/Services/Auth/OtpService.php` with OTP sending and verification methods
  - Extract OTP logic from `UserService` and `AdminService` into `OtpService`
  - Inject `MessageService` dependency
  - _Requirements: 1.1, 1.2, 6.3_

- [x] 5. Create user management services





  - Create `app/Services/User/UserManagementService.php` with create, update, delete methods
  - Create `app/Services/User/UserProfileService.php` with password change and reset methods
  - Extract logic from existing `UserService` into new services
  - Update services to use `UserRepository` for data access
  - Inject required dependencies (GeocodeService, MessageService, OtpService)
  - _Requirements: 1.1, 1.2, 6.1, 6.2, 6.3_

- [x] 6. Create admin management services





  - Create `app/Services/Admin/AdminManagementService.php` with create, update, delete, getAll methods
  - Create `app/Services/Admin/AdminProfileService.php` with password change and reset methods
  - Extract logic from existing `AdminService` into new services
  - Update services to use `AdminRepository` for data access
  - Inject required dependencies (GeocodeService, MessageService, OtpService)
  - _Requirements: 1.1, 1.2, 6.1, 6.2, 6.3_

- [x] 7. Update service provider bindings




  - Update `app/Providers/AppServiceProvider.php` to register all new services
  - Remove or deprecate old service bindings
  - _Requirements: 1.2, 7.5_

## Phase 3: Backend Controller Refactoring

- [x] 8. Create admin authentication controller





  - Create `app/Http/Controllers/Admin/AuthController.php`
  - Move login, logout, forgot password, reset password methods from `AdminController`
  - Update to use new `AdminAuthService` and `OtpService`
  - _Requirements: 1.2, 8.1, 8.2_

- [x] 9. Create admin profile controller










  - Create `app/Http/Controllers/Admin/ProfileController.php`
  - Move profile show, update, change password methods from `AdminController`
  - Update to use new `AdminProfileService`
  - _Requirements: 1.2, 8.1_

- [x] 10. Create admin management controller












  - Create `app/Http/Controllers/Admin/AdminManagementController.php`
  - Move create admin, OTP verification methods from `AdminController`
  - Update to use new `AdminManagementService` and `OtpService`
  - _Requirements: 1.2, 8.1_

- [x] 11. Create admin route controller





  - Create `app/Http/Controllers/Admin/RouteController.php`
  - Move route-to-user, get users list methods from `AdminController`
  - Keep existing location service dependencies
  - _Requirements: 1.2_



- [x] 12. Create user authentication controller



  - Create `app/Http/Controllers/User/AuthController.php`
  - Move login, logout, register, forgot password, reset password methods from `UserController`
  - Update to use new `UserAuthService` and `OtpService`
  - _Requirements: 1.2, 8.1, 8.2_

- [x] 13. Create user profile controller





  - Create `app/Http/Controllers/User/ProfileController.php`
  - Move profile show, update, change password methods from `UserController`
  - Update to use new `UserProfileService`
  - _Requirements: 1.2, 8.1_

- [x] 14. Create user tracking controller




  - Create `app/Http/Controllers/User/TrackingController.php`
  - Move track admin, get admin location methods from `UserController`
  - Keep existing location service dependencies
  - _Requirements: 1.2_

- [x] 15. Update routes to use new controllers





  - Update `routes/web.php` to reference new controller classes
  - Maintain all existing route names and paths
  - Group routes by feature (auth, profile, management, routing, tracking)
  - _Requirements: 2.3, 7.3, 7.5_

- [x] 16. Checkpoint - Backend refactoring complete




  - Ensure all tests pass, ask the user if questions arise
  - Verify all routes work correctly
  - Test authentication flows (login, logout, registration, password reset)
  - Test profile management features
  - Test location/tracking features

## Phase 4: Frontend JavaScript Refactoring

- [x] 17. Create JavaScript feature directory structure





  - Create `resources/js/features/` directory
  - Create subdirectories: `auth/`, `profile/`, `tracking/`, `shared/`
  - Create `resources/js/utils/` directory (if not exists)
  - Create `resources/js/components/` directory (if not exists)
  - _Requirements: 3.1, 3.2, 7.2_

- [x] 18. Move and organize authentication JavaScript files





  - Move `user-registration.js` to `features/auth/user-registration.js`
  - Move `user-forgot-password.js` to `features/auth/user-forgot-password.js`
  - Move `admin-forgot-password.js` to `features/auth/admin-forgot-password.js`
  - Move `admin-create-admin.js` to `features/auth/admin-create.js`
  - Create `features/auth/user-login.js` (extract from inline scripts if needed)
  - Create `features/auth/admin-login.js` (extract from inline scripts if needed)
  - Update import paths in moved files
  - _Requirements: 2.4, 3.1, 3.3, 8.4_

- [x] 19. Move and organize shared JavaScript modules




  - Move `multi-step-form.js` to `features/shared/multi-step-form.js`
  - Move `password-validator.js` to `features/shared/password-validator.js`
  - Update import paths in files that use these modules
  - _Requirements: 3.2, 3.4_

- [x] 20. Move and organize utility JavaScript files




  - Move `form-helpers.js` to `utils/form-helpers.js`
  - Move `validation.js` to `utils/validation.js`
  - Move `map-utils.js` to `utils/map-utils.js`
  - Move `token-manager.js` to `utils/token-manager.js`
  - _Requirements: 3.2_

- [x] 21. Move and organize tracking JavaScript files





  - Move `user-track-admin.js` to `features/tracking/user-track-admin.js`
  - Move `admin-route-map.js` to `features/tracking/admin-route-map.js`
  - Update import paths
  - _Requirements: 3.1, 3.3_

- [x] 22. Move notifications to components





  - Move `notifications.js` to `components/notifications.js`
  - Update import paths in files that use notifications
  - _Requirements: 3.2_

- [x] 23. Update Vite configuration for new JavaScript paths





  - Update `vite.config.js` to include new JavaScript file paths
  - Ensure all feature modules are properly bundled
  - _Requirements: 7.5_

- [x] 24. Update Blade views to reference new JavaScript paths




  - Update all view files that import JavaScript modules
  - Update script tags to use new paths
  - Verify Vite asset references are correct
  - _Requirements: 7.3, 7.5_  

- [x] 25. Checkpoint - JavaScript refactoring complete





  - Ensure all tests pass, ask the user if questions arise
  - Test all forms and interactions
  - Check browser console for errors
  - Verify all JavaScript features work correctly

## Phase 5: CSS Refactoring

- [x] 26. Create CSS directory structure





  - Create `resources/css/base/` directory
  - Create `resources/css/components/` directory
  - Create `resources/css/features/` directory
  - _Requirements: 4.1, 4.2, 4.3, 7.2_

- [x] 27. Create base CSS files





  - Create `base/reset.css` with CSS reset/normalize rules
  - Create `base/typography.css` with font and text styles
  - Create `base/variables.css` with CSS custom properties (colors, spacing, etc.)
  - _Requirements: 4.4_

- [x] 28. Extract and organize component CSS





  - Create `components/buttons.css` with button styles
  - Extract form styles from `forms.css` into `components/forms.css`
  - Create `components/cards.css` with card component styles
  - Create `components/modals.css` with modal dialog styles
  - Create `components/notifications.css` with notification styles
  - _Requirements: 2.5, 4.1, 4.2_

- [x] 29. Extract and organize feature CSS





  - Create `features/auth.css` with authentication page styles
  - Create `features/profile.css` with profile page styles
  - Extract dashboard styles from `pages/admin-dashboard.css` into `features/dashboard.css`
  - Create `features/tracking.css` with map and tracking feature styles
  - _Requirements: 2.5, 4.1, 4.3_

- [x] 30. Update main CSS file with new imports




  - Update `resources/css/app.css` to import all new CSS files
  - Organize imports by category (base, components, features)
  - Maintain proper cascade order
  - _Requirements: 4.5_

- [x] 31. Update Vite configuration for new CSS paths





  - Update `vite.config.js` if needed for CSS processing
  - Ensure all CSS files are properly bundled
  - _Requirements: 7.5_

- [x] 32. Checkpoint - CSS refactoring complete









  - Ensure all tests pass, ask the user if questions arise
  - Verify all pages render correctly
  - Check for any styling issues
  - Test responsive design

## Phase 6: View Refactoring

- [x] 33. Create view directory structure





  - Create `resources/views/layouts/` directory
  - Create feature subdirectories under `admin/` and `user/`
  - _Requirements: 5.4, 7.2_

- [x] 34. Create layout files








  - Create `layouts/app.blade.php` for public pages
  - Create `layouts/admin.blade.php` for admin pages
  - Create `layouts/user.blade.php` for user pages
  - Extract common layout code from existing views
  - _Requirements: 5.4_

- [x] 35. Organize admin views by feature





  - Create `admin/auth/` directory and move login.blade.php, forgot-password.blade.php
  - Create `admin/profile/` directory and move profile.blade.php, change-password.blade.php
  - Create `admin/management/` directory and move create-admin.blade.php
  - Create `admin/routing/` directory and move route-to-user.blade.php
  - Keep dashboard.blade.php in admin root
  - _Requirements: 5.1, 5.3, 7.2_

- [x] 36. Organize user views by feature





  - Create `user/auth/` directory and move login.blade.php, register.blade.php, forgot-password.blade.php
  - Create `user/profile/` directory and move profile.blade.php, change-password.blade.php
  - Create `user/tracking/` directory and move track-admin.blade.php
  - Keep dashboard.blade.php in user root
  - _Requirements: 5.1, 5.3, 7.2_

- [x] 37. Update controller view references





  - Update all controller methods to reference new view paths
  - Use feature-based paths (e.g., 'admin.auth.login' instead of 'admin.login')
  - _Requirements: 7.3, 7.5_



- [x] 38. Update view includes and components






  - Update any @include or @component directives with new paths
  - Verify all view components still work
  - _Requirements: 7.5_

- [x] 39. Checkpoint - View refactoring complete




  - Ensure all tests pass, ask the user if questions arise
  - Verify all views render correctly
  - Test all navigation and links
  - Check for any broken includes or components

## Phase 7: Cleanup and Documentation

- [x] 40. Remove deprecated files





  - Remove old `app/Services/AuthService.php` (replaced by AdminAuthService and UserAuthService)
  - Remove old `app/Services/UserService.php` (replaced by UserManagementService and UserProfileService)
  - Remove old `app/Services/AdminService.php` (replaced by AdminManagementService and AdminProfileService)
  - Remove old `app/Http/Controllers/UserController.php` (replaced by feature controllers)
  - Remove old `app/Http/Controllers/AdminController.php` (replaced by feature controllers)
  - Remove old CSS file `resources/css/pages/admin-dashboard.css` (merged into features/dashboard.css)
  - _Requirements: 7.5_

- [ ] 41. Create README files for major directories
  - Create `app/Repositories/README.md` explaining repository pattern usage
  - Create `app/Services/README.md` explaining service organization
  - Create `resources/js/features/README.md` explaining JavaScript organization
  - Create `resources/css/README.md` explaining CSS organization
  - _Requirements: 7.4_

- [ ] 42. Update project documentation
  - Update main `README.md` with new architecture overview
  - Document the refactoring changes
  - Add developer guide for new structure
  - _Requirements: 7.4, 7.5_

- [ ] 43. Final checkpoint - Complete regression testing
  - Ensure all tests pass, ask the user if questions arise
  - Test all authentication flows end-to-end
  - Test all profile management features
  - Test all location/tracking features
  - Verify no console errors
  - Verify all routes work correctly
  - Confirm all styling is correct
  - Validate performance is unchanged

## Notes

- Each task should be completed and tested before moving to the next
- Maintain Git commits after each major task
- Keep the application functional throughout the refactoring
- Test thoroughly at each checkpoint
- Document any issues or deviations from the plan
