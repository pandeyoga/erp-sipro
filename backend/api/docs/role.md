
# Dokumentasi API Management Role

## Endpoint: Get All Available Permissions

### **GET** `/manage/role/permissions`

### Header
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"base": {
			"role": [
				{
					"label": "Get all Permission Items",
					"code": "role.get_all_permission_items"
				},
				...
			],
			"lead": [
				{
					"label": "Create Lead",
					"code": "lead.create"
				},
				...
			],
			...
		}
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

## Endpoint: Get All Available Role Group

### **GET** `/manage/role/group`

### Header
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"key": "marketing_agent",
			"name": "Marketing Agent"
		},
		{
			"key": "marketing_internal",
			"name": "Marketing Internal"
		},
		{
			"key": "supervisor",
			"name": "Supervisor"
		},
		{
			"key": "director",
			"name": "Director"
		},
		{
			"key": "manager",
			"name": "Manager"
		}
	]
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
```json
{
  "success": false,
  "message": "Unauthorized",
  "errors": null
}
```

## Endpoint: Create New Role

### **POST** `/manage/role`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| name    | `mandatory` Nama Role                   |
| desc    | Deskripsi Role              |
| group    | Grup Role                   |
| permissions | Array of permissions. |
| permissions.* | Nama Permission yang ingin diassign ke role. (harus ada di daftar permission) |

### Contoh Request Body
```json
{
	"name": "Marketing File Uploader",
	"description": "",
	"group": "lead",
	"permissions": [
		"lead.upload_file"
	]
}
```

### Contoh Response Berhasil
> code : 201
```json
{
	"success": true,
	"message": "Role created successfully",
	"data": {
		"name": "Marketing File Uploader",
		"description": null,
		"id": "01966ad1-14f3-72d6-b8f8-684dcee2af5b",
		"group": "lead",
		"updated_at": "2025-04-25T02:40:09.000000Z",
		"created_at": "2025-04-25T02:40:09.000000Z",
		"permissions": [
			{
				"id": "aad894ce-546e-41cd-98a2-e69893ef06c8",
				"role_id": "01966ad1-14f3-72d6-b8f8-684dcee2af5b",
				"permission_code": "lead.upload_file",
				"created_at": "2025-04-25T02:40:09.000000Z",
				"updated_at": "2025-04-25T02:40:09.000000Z"
			}
		]
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Validasi Gagal**
> code : 400
```json
{
	"success": false,
	"message": "Validation error",
	"errors": {
		"permissions": "Invalid permissions"
	}
}
```

---

## Endpoint: Get All Roles

### **GET** `/manage/role`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"id": "01966ad1-14f3-72d6-b8f8-684dcee2af5b",
			"name": "jajanga",
			"description": null,
			"permissions": [
				{
					"name": "Create Lead",
					"code": "lead.create"
				}
			]
		},
		{
			"id": "01966ad0-6845-7369-9db0-d516a178d76b",
			"name": "Marketing 1",
			"description": null,
			"permissions": [
				{
					"name": "Create Lead",
					"code": "lead.create"
				},
				{
					"name": "Update Lead Status",
					"code": "lead.update_status"
				}
			]
		}
	]
}
```

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get All Roles (Untuk Select)

### **GET** `/manage/role/select`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Param Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| search    | Nama Role                   |

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": [
		{
			"id": "01966862-2fa3-71b7-881d-31a1a73549f6",
			"name": "Marketing Lead"
		},
		{
			"id": "0196682e-1e78-712c-8a67-28e0570dd914",
			"name": "Marketing 2"
		}
	]
}
```

### Contoh Response Gagal (jika token tidak valid)
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

---

## Endpoint: Get Role By ID

### **GET** `/manage/role/{role_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Success",
	"data": {
		"id": "0196682e-1e78-712c-8a67-28e0570dd914",
		"name": "Marketing",
		"description": null,
		"permissions": [
			{
				"id": "cf964e54-2981-4817-b3a4-e436ef0eadd7",
				"role_id": "0196682e-1e78-712c-8a67-28e0570dd914",
				"permission_code": "lead.create"
			}
		]
	}
}
```

### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Role Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid role id",
	"errors": null
}
```

**Jika Role Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

---

## Endpoint: Update Role

### **PUT** `/manage/role/{id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
| Parameter | Keterangan                  |
|-----------|-----------------------------|
| name    	| `mandatory` Nama Role       |
| desc    	| Deskripsi Role              |
| group    	| Grup Role                   |
| permissions | Array of permissions. |
| permissions.* | Nama Permission yang ingin diassign ke role. (harus ada di daftar permission) |

### Contoh Request Body
```json
{
	"name": "Marketing File Uploader",
	"description": "",
	"permissions": [
		"lead.create",
		"lead.update"
	]
}
```

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Role updated successfully",
	"data": {
		"id": "01966ad0-6845-7369-9db0-d516a178d76b",
		"name": "Marketing File Uploader",
		"description": null,
		"created_at": "2025-04-25T02:39:25.000000Z",
		"updated_at": "2025-04-25T02:40:44.000000Z",
		"permissions": [
			{
				"id": "dd8af2f4-8778-40ec-add0-b0a2976562a0",
				"role_id": "01966ad0-6845-7369-9db0-d516a178d76b",
				"permission_code": "lead.create",
				"created_at": "2025-04-25T02:40:44.000000Z",
				"updated_at": "2025-04-25T02:40:44.000000Z"
			},
			{
				"id": "0dc96290-5a41-46ba-a6f6-5154116b1938",
				"role_id": "01966ad0-6845-7369-9db0-d516a178d76b",
				"permission_code": "lead.update_status",
				"created_at": "2025-04-25T02:40:44.000000Z",
				"updated_at": "2025-04-25T02:40:44.000000Z"
			}
		]
	}
}
```

### Contoh Response Gagal

**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Validasi Gagal**
> code : 400
```json
{
	"success": false,
	"message": "Validation error",
	"errors": {
		"permissions": "Invalid permissions"
	}
}
```

---

## Endpoint: Delete Role

### **DELETE** `/manage/role/{role_id}`

### Header
```
Content-Type: application/json  
Accept: application/json  
Authorization: Bearer {TOKEN}
```

### Body Request
Tidak ada body yang diperlukan.

### Contoh Response Berhasil
> code : 200
```json
{
	"success": true,
	"message": "Role deleted successfully",
	"data": null
}
```

### Contoh Response Gagal
**Jika Token Tidak Valid**
> code : 401
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**Jika Role Id Tidak Valid**
> code : 400
```json
{
	"success": false,
	"message": "Invalid role id",
	"errors": null
}
```

**Jika Role Tidak Ditemukan**
> code : 404
```json
{
	"success": false,
	"message": "Not Found"
}
```

**Jika Role Sedang Digunakan**
> code : 400
```json
{
	"success": false,
	"message": "Cannot delete role with associated users",
	"errors": null
}
```

---

