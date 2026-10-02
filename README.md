# XplorePondy Travel Itineraries

Version 0.1.1.

Standalone admin-created travel itineraries for XplorePondy. It references existing `listing` posts and does not create or modify Listeo bookings.

## Included in 0.1.1
- `xp_itinerary` admin-only editorial post type
- Day-by-day itinerary builder
- Drag-and-drop itinerary items
- AJAX search of existing `listing` posts
- Listing ID references instead of duplicated listing records
- Basic WooCommerce itinerary product creation when booking is enabled
- Public REST API:
  - `GET /wp-json/xplore/v1/travel-itineraries`
  - `GET /wp-json/xplore/v1/travel-itineraries/{id}`
  - `GET /wp-json/xplore/v1/travel-itineraries/slug/{slug}`

## Important
This first version intentionally does **not** alter Listeo booking, Booking Plus, `user_trip`, or the existing PWA trip system.

WooCommerce date-specific availability, capacity reservation, checkout validation, and the PWA booking UI are subsequent phases and should be implemented against the tested API contract.
