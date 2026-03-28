
# IREMP - Landpro API

### Development server URL
#### Frontend
```
https://erplandpro.vercel.app/
```

#### Backend
```
https://dev-iremp-crm.irondevlab.com/api
```


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

# Daftar Dokumentasi Endpoint

## Dokumentasi Auth
### - [Dokumentasi Autentikasi & Autorisasi](docs/auth.md)
### - [Dokumentasi Role Management](docs/role.md)
### - [Dokumentasi User Management](docs/user.md)

## Dokumentasi CRM
### - [Dokumentasi Dashboard CRM](docs/crm-dashboard.md)
### - [Dokumentasi Contact Management](docs/contact.md)
### - [Dokumentasi Lead Management](docs/lead.md)
### - [Dokumentasi Lead Survey](docs/survey.md)
### - [Dokumentasi Lead Reservation](docs/reservation.md)
### - [Dokumentasi Lead Document](docs/lead-document.md)
### - [Dokumentasi Lead Payment](docs/lead-payment.md)
### - [Dokumentasi Legalitas Akhir](docs/final-legality.md)

## Dokumentasi Property Management
### - [Dokumentasi Project](docs/project.md)
### - [Dokumentasi Cluster](docs/cluster.md)
### - [Dokumentasi Type Unit](docs/type_unit.md)
### - [Dokumentasi Property Unit](docs/property_unit.md)
### - [Dokumentasi Site Plan](docs/siteplan.md)
### - [Dokumentasi Sub Contractor](docs/sub_contractor.md)
### - [Dokumentasi Construction](docs/construction.md)
### - [Dokumentasi Retention Case](docs/retention-case.md)

## Dokumentasi Finance
### - [Dokumentasi Cash Flow](docs/cash-flow.md)
### - [Dokumentasi Cash Flow In](docs/cash-in.md)
### - [Dokumentasi Cash Flow Out](docs/cash-out.md)
### - [Dokumentasi Cash Submission](docs/submission.md)
### - [Dokumentasi Bank Account](docs/bank-account.md)

## Dokumentasi Asset
### - [Dokumentasi Asset](docs/asset.md)

## Dokumentasi Report
### - [Dokumentasi Report Cash Flow](docs/report.md)