# E-Posyandu PWA

## Project Overview

**E-Posyandu** is a Progressive Web Application (PWA) designed as a **Sistem Informasi E-Posyandu Berbasis Web-Mobile** (Web-Mobile Based E-Posyandu Information System). It provides digital health monitoring for toddlers and young children, featuring growth tracking based on the **Indonesian Growth Reference Chart (IGRC) 2018**.

- **Author**: Armaylis Arfa (NIM: 6025063)
- **Course**: Pemrograman Web1 - C
- **Status**: Blueprint + Implementation
- **Technology**: PHP 8.4, MariaDB 12, Docker, PWA

## Prerequisites

Before installing, ensure the following are available:

- **PHP 8.4+** with PDO and pdo_mysql extensions
- **MariaDB 12+** (or MySQL 8+)
- **Docker** and **Docker Compose** (for containerized deployment)
- **Composer** (PHP dependency management)
- **Node.js** v22+ (for PWA service worker build tools)
- **Git** for version control

## Installation Steps

### Local Development (Docker)

1. Clone the repository:
   ```bash
   git clone https://github.com/emoajib/e-posyandu.git
   cd e-posyandu
   ```

2. Copy the environment template and configure:
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

3. Start all services:
   ```bash
   docker-compose up -d
   ```

4. Access the application:
   - Main app: http://localhost
   - Database: localhost:3306
   - MariaDB root password: from `.env`

### Manual Installation

1. Install PHP dependencies:
   ```bash
   composer install
   ```

2. Create the database:
   ```sql
   CREATE DATABASE e_posyandu;
   CREATE USER 'salsabil'@'localhost' IDENTIFIED BY '';
   GRANT ALL PRIVILEGES ON e_posyandu.* TO 'salsabil'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. Configure `config/database.php` with your credentials.

4. Set up the web server (Apache/Nginx) pointing to the `public/` directory.

## API Endpoints

### Authentication
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/login.php` | POST | User login with CSRF token |
| `/logout.php` | GET | Session destruction and logout |

### Balita (Child Data)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/modules/balita/index.php` | GET | List all children |
| `/modules/balita/tambah.php` | GET/POST | Add new child record |
| `/modules/balita/simpan.php` | POST | Save child data |
| `/modules/balita/edit.php` | GET/POST | Edit child record |
| `/modules/balita/hapus.php` | POST | Delete child record |

### Timbangan (Weight Measurement)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/modules/timbangan/index.php` | GET | View weight records |
| `/modules/timbangan/tambah.php` | GET/POST | Add weight measurement |
| `/modules/timbangan/simpan.php` | POST | Save weight data with IGRC stunting analysis |

### Laporan (Reports)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/modules/laporan/cetak.php` | GET | Print report with JOIN queries |

### Gizi (Nutrition)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/modules/gizi/index.php` | GET | Nutrition information (offline-capable) |

## IGRC 2018 Reference

The **Indonesian Growth Reference Chart 2018 (IGRC 2018)** is the primary standard used for growth assessment in this application. Key specifications:

- **Source**: Kemenkes RI (Ministry of Health Republic of Indonesia)
- **Reference**: Pulungan 2018, Novina 2020
- **Cutoff**: -2 SD (same as WHO, but with Indonesia-specific median)
- **Mean height z-score**: -2.03 SD (vs WHO standard)
- **Key finding**: IGRC results in LOWER stunting prevalence compared to WHOCGS

### Why IGRC 2018?

The WHO Child Growth Standards, while internationally recognized, may not accurately reflect Indonesian children's growth patterns. The IGRC 2018 provides:

1. **Indonesia-specific medians** derived from national survey data
2. **More accurate stunting detection** for the Indonesian population
3. **Lower false-positive rates** compared to WHO standards
4. **Policy alignment** with Kemenkes RI guidelines

### Implementation Notes

- All growth calculations use **-2 SD cutoff** from IGRC medians
- Weight-for-age, height-for-age, and weight-for-height Z-scores are computed
- The system flags children below -2 SD as potentially stunted
- Input validation ensures age, weight, and height fall within IGRC-valid ranges

## Deployment Guide (Rumahweb VPS)

### VPS Specifications

| Parameter | Value |
|-----------|-------|
| **Provider** | Rumahweb Indonesia |
| **Package** | VPS XS |
| **Resources** | 1 vCPU, 512 MB RAM, 10 GB SSD |
| **OS** | Ubuntu 20.04 x86_64 |
| **Location** | Zone A - TechnoVillage Bogor (Tier 3) |
| **Price** | ~Rp 50,000/month (~$3 USD) |
| **Hostname** | e-posyandu.com |
| **Network** | IIX, JKT-IX, BIX, EPIX backbone |

### Deployment Steps

1. **Purchase VPS** from Rumahweb and obtain SSH credentials
2. **Run the deployment script**:
   ```bash
   chmod +x deploy-vps.sh
   ./deploy-vps.sh
   ```
3. **Configure DNS** to point `e-posyandu.com` to your VPS IP
4. **Verify deployment**:
   ```bash
   ssh root@YOUR_VPS_IP
   docker ps
   docker-compose logs
   ```

### Manual VPS Deployment

If the automated script doesn't work:

```bash
# SSH into VPS
ssh root@YOUR_VPS_IP

# Install Docker
curl -fsSL https://get.docker.com | sh
sudo systemctl enable docker
sudo systemctl start docker

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Clone and deploy
cd /opt
git clone https://github.com/emoajib/e-posyandu.git
cd e-posyandu
cp .env.example .env
# Edit .env
docker-compose up -d

# Setup SSL
apt install certbot python3-certbot-nginx
certbot --nginx -d e-posyandu.com
```

## Security Notes

This application implements the following security measures:

- **PDO Prepared Statements**: All database queries use parameterized queries (NOT mysqli_* interpolation)
- **CSRF Tokens**: Every form includes a CSRF token for validation
- **Password Hashing**: `password_hash()` and `password_verify()` for all authentication
- **Output Sanitization**: `htmlspecialchars()` applied to all user-facing output
- **Session Management**: Session regeneration on login and privilege changes
- **Input Validation**: Age, weight, and height validated against IGRC ranges
- **Error Logging**: All errors logged to file, not displayed to users
- **Security Headers**: X-Frame-Options, X-Content-Type-Options, HSTS, CSP
- **SSL/TLS**: All traffic encrypted via HTTPS

### Security Scanning

The CI pipeline includes:
- PHP linting via `vendor/bin/phpcs`
- Security scan for insecure `mysqli_query`/`mysql_query` patterns
- Automated testing with PHPUnit

## Docker Usage

### Available Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild after code changes
docker-compose build --no-cache && docker-compose up -d

# View logs
docker-compose logs -f app
docker-compose logs -f db

# Access database container
docker-compose exec db mysql -u salsabil -p e_posyandu

# Run PHP commands
docker-compose exec app php artisan list
```

### Architecture

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    Nginx    │────▶│    PHP      │────▶│   MariaDB   │
│  (Port 443) │     │  (Port 80)  │     │ (Port 3306) │
│  SSL/TLS    │     │  Apache     │     │  InnoDB     │
│  Security   │     │  PHP 8.4    │     │  MariaDB 12 │
│  Headers    │     │  PDO/MySQL  │     │             │
└─────────────┘     └─────────────┘     └─────────────┘
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- **Kemenkes RI** for the IGRC 2018 dataset
- **Pulungan H.** for the foundational research on Indonesian growth standards
- **Novina F.** for the comparative analysis of IGRC vs WHO
- **Rumahweb Indonesia** for affordable VPS hosting

---

**E-Posyandu** — Digital Health Monitoring for Indonesian Children 🇮🇩
