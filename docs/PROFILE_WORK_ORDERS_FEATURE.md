# Profile Work Orders Feature

## Overview
This feature allows users to view their work orders (commits/commitments) directly from their profile page, providing quick access to their most recent interventions and tasks.

## Implementation

### User Profile Page Enhancement
The user profile page now displays a "Mes Engagements" (My Commitments) section showing:
- The 5 most recent work orders relevant to the user
- Status indicators (completed, in progress, failed, etc.)
- Priority badges (urgent, high, normal, low)
- Equipment information
- Quick navigation to full work order details

### Role-Based Display

#### Employees (employe)
- See work orders they **created**
- View title: "Demandes d'intervention" (Intervention Requests)
- Shows work orders from the `employeWorkOrders` relationship
- Click action: Navigate to work orders index page

#### Technicians (technicien/technician)
- See work orders **assigned to them**
- View title: "Interventions assignées" (Assigned Interventions)
- Shows work orders from the `technicienWorkOrders` relationship
- Click action: Navigate to specific work order details

#### Admins
- See **all recent work orders** in the system
- View title: "Ordres de travail récents" (Recent Work Orders)
- Shows most recent work orders across all users
- Click action: Navigate to specific work order details

## Technical Details

### Files Modified
1. **app/Http/Controllers/ProfileController.php**
   - Added work order fetching logic in the `edit()` method
   - Loads related data (equipement, technicien, employe) for efficiency

2. **app/Models/User.php**
   - Added `isAdmin()` helper method
   - Added `isTechnician()` helper method (handles 'technicien' and 'technician')
   - Added `isEmployee()` helper method

3. **resources/views/profile/edit.blade.php**
   - Added "Mes Engagements" card section
   - Displays work orders with icons, badges, and navigation
   - Empty state for users with no work orders

### Features
- **Status Icons**: Visual indicators for work order status
  - ✅ Green check: Completed (terminee)
  - ⏱️ Blue clock: In progress (en_cours)
  - ❌ Red X: Failed (echec)
  - ℹ️ Gray info: Other statuses

- **Priority Badges**: Color-coded priority indicators
  - Red: Urgent (urgente)
  - Orange: High (haute)
  - Blue: Normal (normale)
  - Gray: Low (basse)

- **Hover Effects**: Interactive elements that reveal navigation options

- **Quick Access**: Link to view all work orders at the bottom of the card

## User Experience Flow

1. User navigates to their profile page (`/profile`)
2. Profile page displays user information and recent work orders
3. User can see at a glance:
   - Their most recent 5 work orders
   - Status of each work order
   - Priority level
   - Related equipment
   - How long ago it was created
4. User can click on a work order to see more details
5. User can click "Voir tous mes engagements" to see all their work orders

## Benefits

### For Users
- Quick access to their work orders without navigating away from profile
- Visual status indicators make it easy to see what needs attention
- Reduces clicks needed to check on work orders

### For the Application
- Improves user engagement
- Provides context-aware information based on user role
- Follows existing design patterns and UI/UX conventions

## Known Limitations
- Employees navigate to the work orders index page instead of individual work order details (no show route implemented yet)
- Limited to 5 most recent work orders on profile (full list available via "View All" link)

## Future Enhancements
- Consider implementing a show route for employee work orders for consistency
- Add filtering options (by status, priority) in the profile view
- Add pagination or "load more" functionality for users with many work orders
- Add real-time updates when work order status changes
