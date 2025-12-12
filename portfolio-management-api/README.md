## Portfolio Management API

This project is a Laravel-based REST API for managing an admin portfolio, including profile, projects, skills, experiences, and a public portfolio view.

All API responses are JSON.

Base URL examples:

- **Local**: `http://localhost:8000/api`
- **Production**: `/api`

---

## Authentication & Authorization

- **Authentication mechanism**: Laravel Sanctum personal access tokens.
- **Who authenticates**: `Admin` users (see `App\Models\Admin`).
- **Token creation**: On successful login, a Sanctum token is issued and must be sent on subsequent requests.
- **Token revocation**: Logout deletes the current access token.

### Login Flow

1. Client sends credentials to `POST /api/auth/login`.
2. If valid:
   - Existing tokens for the admin are deleted.
   - A new token is generated.
3. Client stores the token and uses it in the `Authorization` header:

```http
Authorization: Bearer <token>
Accept: application/json
```

### Logout Flow

1. Client calls `POST /api/auth/logout` with the `Authorization: Bearer <token>` header.
2. The current token is revoked and becomes unusable.

### Security / Middleware Dependencies

- **`auth:sanctum`**: Protects routes; requires a valid Sanctum token associated with an `Admin`.
- **`json.accepts`**: Custom middleware that likely enforces JSON requests (for example, `Accept: application/json`).
- **Form Request validation**:
  - `App\Http\Requests\Auth\LoginRequest`
  - `App\Http\Requests\Profile\UpdateProfileRequest`
  - `App\Http\Requests\Project\ProjectRequest`
  - `App\Http\Requests\Skill\SkillRequest`
- **API Resources** for consistent responses:
  - `AdminResource`, `ProjectResource`, `SkillResource`, `ExperienceResource`

Unless explicitly stated otherwise, all protected endpoints require:

- **Headers**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

---

## Endpoint Summary

- **Auth**
  - `POST /auth/login` – Login and obtain token.
  - `POST /auth/logout` – Logout and revoke current token.
- **Profile**
  - `POST /profile/update` – Update admin profile (including profile image).
- **Projects**
  - `GET /projects` – List projects for authenticated admin.
  - `POST /projects/create` – Create a new project.
  - `GET /projects/{project}` – Get a single project by ID.
  - `PUT /projects/update/{project}` – Update a project by ID.
  - `DELETE /projects/delete/{project}` – Delete a project by ID.
- **Skills** (`Route::apiResource('skills', SkillController::class)->except(['create','edit','show'])`)
  - `GET /skills` – List skills.
  - `POST /skills` – Create a new skill.
  - `PUT /skills/{skill}` – Update a skill.
  - `DELETE /skills/{skill}` – Delete a skill.
- **Experiences** (`Route::apiResource('experiences', ExperienceController::class)->except(['create','edit','show'])`)
  - `GET /experiences` – List experiences.
  - `POST /experiences` – Create a new experience.
  - `PUT /experiences/{experience}` – Update an experience.
  - `DELETE /experiences/{experience}` – Delete an experience.
- **Public Portfolio**
  - `GET /public/portfolio` – Public, read-only portfolio data (no auth required).
- **Health Check**
  - `GET /test` – Simple JSON response to verify connection.

> Note: All paths above are relative to the `/api` prefix.

---

## Detailed Endpoint Documentation

### 1. Auth – Login

- **Endpoint Name**: Login
- **URL and Method**: `POST /api/auth/login`
- **Authentication**: Not required
- **Header Requirements**:
  - `Accept: application/json`
  - `Content-Type: application/json`

#### Sample Request

```http
POST /api/auth/login HTTP/1.1
Host: localhost:8000
Accept: application/json
Content-Type: application/json

{
  "username": "admin",
  "password": "secret-password"
}
```

#### Sample Response (200 OK)

```json
{
  "token": "1|example-long-token",
  "token_type": "Bearer",
  "admin": {
    // AdminResource fields
  }
}
```

#### Error Responses

- **422 Unprocessable Entity** – Validation errors (e.g. missing `username` or `password`).
- **422 Unprocessable Entity** – Invalid credentials:

```json
{
  "errors": {
    "username": ["The provided credentials are invalid."]
  }
}
```

#### Functionality

Validates admin credentials and, if correct, issues a new Sanctum token while revoking previous tokens for the same admin.

---

### 2. Auth – Logout

- **Endpoint Name**: Logout
- **URL and Method**: `POST /api/auth/logout`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Request

```http
POST /api/auth/logout HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
```

#### Sample Response (200 OK)

```json
{
  "message": "Logged out successfully."
}
```

#### Error Responses

- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Deletes the currently used Sanctum token, effectively logging out the authenticated admin.

---

### 3. Profile – Update Profile

- **Endpoint Name**: Update Profile
- **URL and Method**: `POST /api/profile/update`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data`

#### Sample Request

```http
POST /api/profile/update HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
Content-Type: multipart/form-data; boundary=---BOUNDARY

-----BOUNDARY
Content-Disposition: form-data; name="name"

John Doe
-----BOUNDARY
Content-Disposition: form-data; name="bio"

Backend developer
-----BOUNDARY
Content-Disposition: form-data; name="profile_image"; filename="avatar.png"
Content-Type: image/png

<binary image content>
-----BOUNDARY--
```

*(Exact fields depend on `UpdateProfileRequest`.)*

#### Sample Response (200 OK)

```json
{
  "message": "Profile updated successfully.",
  "profile": {
    // AdminResource fields with updated data
  }
}
```

#### Error Responses

- **422 Unprocessable Entity** – Validation errors (e.g. invalid file type, missing required fields).
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Updates the authenticated admin’s profile fields and optionally replaces the stored profile image, deleting the previous file if present.

---

### 4. Projects – List Projects

- **Endpoint Name**: List Projects
- **URL and Method**: `GET /api/projects`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
[
  {
    // ProjectResource fields
  }
]
```

#### Error Responses

- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Returns a list of projects belonging to the authenticated admin, sorted from newest to oldest.

---

### 5. Projects – Create Project

- **Endpoint Name**: Create Project
- **URL and Method**: `POST /api/projects/create`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data` (supports `images[]` upload)

#### Sample Request

```http
POST /api/projects/create HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
Content-Type: multipart/form-data; boundary=---BOUNDARY

-----BOUNDARY
Content-Disposition: form-data; name="title"

My Project
-----BOUNDARY
Content-Disposition: form-data; name="description"

Project description...
-----BOUNDARY
Content-Disposition: form-data; name="images[]"; filename="screenshot1.png"
Content-Type: image/png

<binary>
-----BOUNDARY--
```

*(Exact fields depend on `ProjectRequest`.)*

#### Sample Response (201 Created)

```json
{
  // ProjectResource fields
}
```

#### Error Responses

- **422 Unprocessable Entity** – Validation errors.
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Creates a new project for the authenticated admin and uploads any attached images to `storage/app/public/uploads/projects`.

---

### 6. Projects – Get Single Project

- **Endpoint Name**: Get Project
- **URL and Method**: `GET /api/projects/{project}`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
{
  // ProjectResource fields
}
```

#### Error Responses

- **404 Not Found** – Project not found.
- **403 Forbidden** – Authenticated admin is not the owner:

```json
{
  "message": "You are not allowed to modify this project."
}
```

#### Functionality

Returns a single project resource if it belongs to the authenticated admin.

---

### 7. Projects – Update Project

- **Endpoint Name**: Update Project
- **URL and Method**: `PUT /api/projects/update/{project}`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data` or `application/json` (depending on payload)

#### Sample Request (JSON)

```http
PUT /api/projects/update/1 HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
Content-Type: application/json

{
  "title": "Updated Project Title"
}
```

#### Sample Response (200 OK)

```json
{
  // Updated ProjectResource fields
}
``>

#### Error Responses

- **422 Unprocessable Entity** – Validation errors.
- **403 Forbidden** – Not owner.
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Validates and updates a project belonging to the authenticated admin, including optional images if sent.

---

### 8. Projects – Delete Project

- **Endpoint Name**: Delete Project
- **URL and Method**: `DELETE /api/projects/delete/{project}`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
{
  "message": "Project deleted successfully."
}
```

#### Error Responses

- **403 Forbidden** – Not owner.
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Deletes a project if it belongs to the authenticated admin.

---

### 9. Skills – List Skills

- **Endpoint Name**: List Skills
- **URL and Method**: `GET /api/skills`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
[
  {
    // SkillResource fields
  }
]
```

#### Error Responses

- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Returns a list of the authenticated admin’s skills, ordered by proficiency (descending) and then by name.

---

### 10. Skills – Create Skill

- **Endpoint Name**: Create Skill
- **URL and Method**: `POST /api/skills`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: application/json`

#### Sample Request

```http
POST /api/skills HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
Content-Type: application/json

{
  "name": "Laravel",
  "proficiency": 90
}
```

*(Exact fields depend on `SkillRequest`.)*

#### Sample Response (201 Created)

```json
{
  // SkillResource fields
}
```

#### Error Responses

- **422 Unprocessable Entity** – Validation errors.
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Creates a new skill for the authenticated admin.

---

### 11. Skills – Update Skill

- **Endpoint Name**: Update Skill
- **URL and Method**: `PUT /api/skills/{skill}`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`
  - `Content-Type: application/json`

#### Sample Request

```http
PUT /api/skills/1 HTTP/1.1
Host: localhost:8000
Accept: application/json
Authorization: Bearer 1|example-long-token
Content-Type: application/json

{
  "proficiency": 95
}
```

#### Sample Response (200 OK)

```json
{
  // Updated SkillResource fields
}
```

#### Error Responses

- **422 Unprocessable Entity** – Validation errors.
- **403 Forbidden** – Authenticated admin is not the owner:

```json
{
  "message": "You are not allowed to modify this skill."
}
```

- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Updates an existing skill that belongs to the authenticated admin.

---

### 12. Skills – Delete Skill

- **Endpoint Name**: Delete Skill
- **URL and Method**: `DELETE /api/skills/{skill}`
- **Authentication**: Required (`auth:sanctum`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
{
  "message": "Skill deleted successfully."
}
```

#### Error Responses

- **403 Forbidden** – Not owner.
- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Deletes a skill that belongs to the authenticated admin.

---

### 13. Experiences – Resource Endpoints

The `ExperienceController` is registered with:

```php
Route::apiResource('experiences', ExperienceController::class)->except(['create', 'edit', 'show']);
```

Intended endpoints:

- **List Experiences**
  - `GET /api/experiences`
- **Create Experience**
  - `POST /api/experiences`
- **Update Experience**
  - `PUT /api/experiences/{experience}`
- **Delete Experience**
  - `DELETE /api/experiences/{experience}`

> Implementation in `ExperienceController` is currently skeletal (`//` placeholders). Once implemented, responses will likely mirror the pattern used in `ProjectController` and `SkillController` (JSON resources, validation, and ownership checks).

Common behavior (intended):

- Protected by `auth:sanctum`.
- Request validation with a dedicated form request (once added).
- Ownership checks to ensure only the authenticated admin can modify their experiences.

---

### 14. Public Portfolio – Get Public Portfolio

- **Endpoint Name**: Public Portfolio
- **URL and Method**: `GET /api/public/portfolio`
- **Authentication**: Not required
- **Header Requirements**:
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
{
  "profile": {
    // AdminResource fields
  },
  "projects": [
    // ProjectResource collection; only published and latest first
  ],
  "skills": [
    // SkillResource collection; ordered by proficiency desc, then name
  ],
  "experiences": [
    // ExperienceResource collection; ordered by start_date desc
  ]
}
```

#### Error Responses

- **404 Not Found** – If no `Admin` exists yet:

```json
{
  "message": "Portfolio has not been configured yet."
}
```

#### Functionality

Provides a read-only public view of the portfolio, aggregating profile, projects, skills, and experiences from the first `Admin`.

---

### 15. Health Check – Test Connection

- **Endpoint Name**: Test Connection
- **URL and Method**: `GET /api/test`
- **Authentication**: Required (`auth:sanctum`, `json.accepts`)
- **Header Requirements**:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

#### Sample Response (200 OK)

```json
{
  "message": "Connection successful!"
}
```

#### Error Responses

- **401 Unauthorized** – Missing or invalid token.

#### Functionality

Simple endpoint used to verify that authentication and routing are working correctly.

---

## Dependencies & Middleware (Security / Request Processing)

- **Laravel Sanctum** – Token-based API authentication.
- **`auth:sanctum` middleware** – Guards protected routes using Sanctum tokens.
- **`json.accepts` middleware** – Enforces that requests expect JSON responses (e.g. `Accept: application/json`).
- **Form Requests** (`LoginRequest`, `UpdateProfileRequest`, `ProjectRequest`, `SkillRequest`) – Centralized validation and authorization for incoming data.
- **API Resources** (`AdminResource`, `ProjectResource`, `SkillResource`, `ExperienceResource`) – Shape and sanitize API JSON responses.

When integrating with this API, always:

- Send `Accept: application/json`.
- Use `Authorization: Bearer <token>` for any protected endpoint.
- Respect validation rules defined by the Form Request classes.


