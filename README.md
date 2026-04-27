# AWS S3 Storage Class Learning App

A Laravel web application for hands-on learning of all 7 Amazon S3 storage classes. Upload real files to S3, change their storage class, and see the differences in cost, retrieval time, and behavior.

**Live Demo:** [https://aws-s3-learning.onrender.com](https://aws-s3-learning.onrender.com)

---

## What You Can Do

| Feature | Description |
|---------|-------------|
| **Upload files** | Choose a file + select any S3 storage class at upload time |
| **List files** | See all uploaded objects with their current storage class, size, and last modified date |
| **Change storage class** | Switch any object between all 7 S3 storage classes instantly |
| **Presigned URL** | Generate a time-limited (5 min) URL to access private objects |
| **Delete files** | Remove objects from S3 |
| **Reference panel** | Side-by-side comparison of all 7 storage classes with retrieval time, min storage, and best use case |

## S3 Storage Classes Covered

| Class | Code | Retrieval | Min Storage | Best For |
|-------|------|-----------|-------------|----------|
| S3 Standard | `STANDARD` | Milliseconds | None | Frequently accessed data |
| S3 Standard-IA | `STANDARD_IA` | Milliseconds | 30 days | Infrequent access, backups |
| S3 One Zone-IA | `ONEZONE_IA` | Milliseconds | 30 days | Non-critical, re-creatable data |
| S3 Glacier Instant Retrieval | `GLACIER_IR` | Milliseconds | 90 days | Archives needing immediate access |
| S3 Glacier Flexible Retrieval | `GLACIER` | Minutes–hours | 90 days | Rarely accessed archives |
| S3 Glacier Deep Archive | `DEEP_ARCHIVE` | 12–48 hours | 180 days | Long-term compliance retention |
| S3 Intelligent-Tiering | `INTELLIGENT_TIERING` | Milliseconds | None | Unknown/changing access patterns |

## Tech Stack

- **Laravel 13** (PHP 8.4)
- **AWS SDK for PHP** (S3Client)
- **Bootstrap 5** (UI)
- **Docker** (deployment)

## Project Structure

```
app/
├── Http/Controllers/S3Controller.php   # Route handlers
└── Services/S3Service.php              # AWS S3 SDK wrapper
resources/views/index.blade.php          # Single-page UI
routes/web.php                          # 5 routes
Dockerfile                              # Render deployment
```

## Local Setup

### Prerequisites

- PHP 8.2+
- Composer
- AWS account with S3 access

### Steps

1. **Clone the repo:**
   ```bash
   git clone https://github.com/MAWOW1000/aws-s3-learning.git
   cd aws-s3-learning
   ```

2. **Install dependencies:**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure AWS credentials** in `.env`:
   ```env
   AWS_ACCESS_KEY_ID=your_access_key
   AWS_SECRET_ACCESS_KEY=your_secret_key
   AWS_DEFAULT_REGION=ap-southeast-2
   AWS_BUCKET=your-bucket-name
   ```

4. **Create an S3 bucket** (if you don't have one):
   ```bash
   aws s3 mb s3://your-unique-bucket-name --region ap-southeast-2
   ```

5. **Run the app:**
   ```bash
   php artisan serve
   ```

6. **Open** [http://127.0.0.1:8000](http://127.0.0.1:8000)

## IAM Policy (Minimum Required)

Create an IAM user and attach this policy:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket",
                "s3:GetBucketLocation",
                "s3:ListBucketMultipartUploads",
                "s3:CreateBucket",
                "s3:DeleteBucket"
            ],
            "Resource": "arn:aws:s3:::*"
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:PutObjectAcl",
                "s3:PutObjectTagging",
                "s3:AbortMultipartUpload",
                "s3:ListMultipartUploadParts"
            ],
            "Resource": "arn:aws:s3:::*/*"
        }
    ]
}
```

## Key Concepts Learned

- **Storage class transitions** — `copyObject` with a new `StorageClass` changes the tier
- **Versioning** — If enabled, class changes create new versions; if disabled, objects are overwritten
- **Glacier archival** — Objects in `GLACIER` or `DEEP_ARCHIVE` cannot be accessed until restored
- **Presigned URLs** — Temporary access to private objects without making the bucket public
- **Cost tradeoffs** — Cheaper storage = slower retrieval or minimum storage duration charges

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
