# Frontend GUI - Quick Start Guide

## 📍 Page Routes

| Page | Route | Description |
|------|-------|-------------|
| Dashboard | `/dashboard` | Home page with statistics and quick links |
| Cities | `/cities` | Manage all cities - Create, Read, Update, Delete |
| Counties | `/counties` | Manage all counties - Create, Read, Update, Delete |

## 🎯 What Each Page Does

### Dashboard (`/dashboard`)
```
┌─────────────────────────────────────────┐
│  Welcome, [User Name]!                  │
│  Here's an overview of your data        │
├─────────────────────────────────────────┤
│                                         │
│  [Total Cities]  [Total Counties] [Users] │
│                                         │
├─────────────────────────────────────────┤
│  Quick Actions                          │
│  • Manage Cities  • Manage Counties      │
├─────────────────────────────────────────┤
│  Recently Added Cities (Table)          │
│  Zip  | City Name | County              │
└─────────────────────────────────────────┘
```

### Cities Page (`/cities`)
```
┌─────────────────────────────────────────┐
│  Cities                                 │
│  Manage all cities in the system   [+]  │
├─────────────────────────────────────────┤
│  [Search box]        [County Filter]    │
├─────────────────────────────────────────┤
│  Zip  │ Name │ County │ Actions         │
├──────┼──────┼────────┼─────────────────┤
│  1001 │ City1 │ County1 │ [Edit] [Del]  │
│  1002 │ City2 │ County2 │ [Edit] [Del]  │
│  1003 │ City3 │ County1 │ [Edit] [Del]  │
├─────────────────────────────────────────┤
│  Page 1 of X  [< Prev | 1 2 3 | Next >] │
└─────────────────────────────────────────┘

CREATE/EDIT MODAL:
┌────────────────────────┐
│  Create New City       │
├────────────────────────┤
│  Zip Code: [_______]   │
│  City Name: [_______]  │
│  County: [dropdown]    │
├────────────────────────┤
│  [Cancel]  [Create]    │
└────────────────────────┘
```

### Counties Page (`/counties`)
```
┌─────────────────────────────────────────┐
│  Counties                               │
│  Manage all counties in system     [+]  │
├─────────────────────────────────────────┤
│  [Search counties...]                   │
├─────────────────────────────────────────┤
│  Name │ Cities Count │ Actions          │
├──────┼──────────────┼─────────────────┤
│ County1 │ 5 cities  │ [Edit] [Del]     │
│ County2 │ 3 cities  │ [Edit] [Del]     │
│ County3 │ 0 cities  │ [Edit] [Del]     │
├─────────────────────────────────────────┤
│  Page 1 of X  [< Prev | 1 2 3 | Next >] │
└─────────────────────────────────────────┘

CREATE/EDIT MODAL:
┌────────────────────────┐
│  Create New County     │
├────────────────────────┤
│  County Name: [_____]  │
├────────────────────────┤
│  [Cancel]  [Create]    │
└────────────────────────┘
```

## 🎮 How to Use

### Adding a City
1. Go to `/cities`
2. Click the blue "[+] Add City" button
3. Fill in:
   - **Zip Code**: City postal code (e.g., "12345")
   - **City Name**: Name of the city (e.g., "New York")
   - **County**: Select from dropdown
4. Click "Create City"
5. ✅ Success message appears!

### Editing a City
1. Go to `/cities`
2. Find the city in the table
3. Click the pencil (✏️) icon in Actions
4. Modal opens with current data
5. Make changes
6. Click "Save Changes"
7. ✅ Updated!

### Deleting a City
1. Go to `/cities`
2. Find the city in the table
3. Click the trash (🗑️) icon in Actions
4. Confirm deletion
5. ✅ Deleted!

### Searching Cities
1. Go to `/cities`
2. Type in search box (searches by name or zip)
3. Results update in real-time
4. Clear search to see all

### Filtering Cities by County
1. Go to `/cities`
2. Use "Filter by county..." dropdown
3. Select a county
4. Table shows only that county's cities
5. Click "All Counties" to reset

### Sorting Cities
1. Go to `/cities`
2. Click on column header ("Zip" or "Name")
3. Table sorts by that column
4. Click again to reverse order (↑ ascending, ↓ descending)

---

### Adding a County
1. Go to `/counties`
2. Click the blue "[+] Add County" button
3. Enter **County Name** (must be unique)
4. Click "Create County"
5. ✅ Success!

### Editing a County
1. Go to `/counties`
2. Find the county in the table
3. Click the pencil (✏️) icon
4. Modal opens
5. Edit the name
6. Click "Save Changes"
7. ✅ Updated!

### Deleting a County
⚠️ **Important**: You can only delete a county if it has NO cities!

1. Go to `/counties`
2. Find the county with "0 cities"
3. Click the trash (🗑️) icon
4. ✅ Deleted!

If a county has cities, you'll see an error: "Cannot delete county with associated cities!"
- Delete the cities first, OR
- Move cities to another county

### Searching Counties
1. Go to `/counties`
2. Type in search box
3. Results update in real-time

---

## 🌓 Dark Mode

The interface automatically detects your system preference and switches between:
- ☀️ **Light Mode** (white background, dark text)
- 🌙 **Dark Mode** (dark background, light text)

Toggle in your browser or OS settings to see it change!

## 📊 Dashboard Quick Stats

The dashboard shows:
- **Total Cities**: How many cities are in the system
- **Total Counties**: How many counties are in the system
- **Total Users**: How many user accounts exist
- **Recently Added**: Last 5 cities added to the system

Click "Manage Cities" or "Manage Counties" buttons to go directly to those pages.

## 🔒 Authentication

- You must be **logged in** to access any management pages
- If not logged in, you're redirected to `/login`
- Create an account at `/register` if you don't have one
- Your account can have settings and security options

## ⚡ Tips & Tricks

✨ **Pro Tips:**
- Use search + filters together for faster navigation
- Pagination shows 15 items per page
- Sort by "Name" to find cities/counties quickly
- Modal dialogs validate your input before saving
- All operations show success/error messages
- Data is persisted in database immediately

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| "County with cities" error | Delete cities first or remove their county association |
| Search not working | Make sure you typed correctly, try simpler terms |
| Can't see counties in dropdown | Create counties first from `/counties` page |
| Pagination not working | Try refreshing the page |
| Modal closed accidentally | Data isn't saved, just reopen |

---

**All set!** Navigate the app using the sidebar menu. 🎉
