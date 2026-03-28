
# IREMP - Landpro

### Base URL
```
/api
```

---

## Struktur Respons

### **Response Berhasil**
```json
{
  "success": true,
  "message": "Deskripsi keberhasilan",
  "data": { ... }
}
```

### **Response Error**
```json
{
  "success": false,
  "message": "Deskripsi kesalahan",
  "errors": { ... }
}
```

### **Response dengan Paginasi**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [ ... ],
  "pagination": {
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "last_page": 10
  }
}
```

---

## Daftar Dokumentasi Endpoint

### - [Dokumentasi Autentikasi & Autorisasi](docs/auth.md)
### - [Dokumentasi Role Management](docs/role.md)