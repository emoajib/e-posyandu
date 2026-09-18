#!/bin/bash
# Deploy to Rumahweb VPS (Ubuntu 20.04, Zone A TechnoVillage)
# Author: E-Posyandu Team
set -e

VPS_HOST="e-posyandu.com"
VPS_USER="root"
VPS_IP="YOUR_VPS_IP"

echo "=== E-Posyandu VPS Deployment ==="
echo "Target: Rumahweb VPS - Zone A TechnoVillage Bogor"

# Step 1: SSH into VPS and update
echo "Step 1: SSH and update system..."
ssh ${VPS_USER}@${VPS_IP} << 'REMOTE'
sudo apt-get update -y
sudo apt-get upgrade -y
sudo apt-get install -y docker.io docker-compose git curl nginx
sudo systemctl enable docker
sudo systemctl start docker
sudo usermod -aG docker $USER
REMOTE

# Step 2: Clone repository
echo "Step 2: Clone repository..."
ssh ${VPS_USER}@${VPS_IP} << 'REMOTE'
cd /opt
git clone https://github.com/emoajib/e-posyandu.git
cd e-posyandu
REMOTE

# Step 3: Configure .env
echo "Step 3: Configure environment..."
ssh ${VPS_USER}@${VPS_IP} << 'REMOTE'
cd /opt/e-posyandu
cat > .env << 'ENVEOF'
DB_ROOT_PASSWORD=your_secure_root_password
DB_NAME=e_posyandu
DB_USER=salsabil
ENVEOF
REMOTE

# Step 4: Build and deploy
echo "Step 4: Build and deploy with Docker..."
ssh ${VPS_USER}@${VPS_IP} << 'REMOTE'
cd /opt/e-posyandu
docker-compose build
docker-compose up -d
REMOTE

# Step 5: SSL setup
echo "Step 5: Setting up SSL..."
ssh ${VPS_USER}@${VPS_IP} << 'REMOTE'
sudo apt-get install -y certbot python3-certbot-nginx
sudo certbot --nginx -d e-posyandu.com --non-interactive --agree-tos -m admin@e-posyandu.com
REMOTE

echo "=== Deployment Complete ==="
echo "E-Posyandu is now live at https://e-posyandu.com"
