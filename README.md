# AWS S3 Learning Project

A hands-on project to learn Amazon S3 (Simple Storage Service) using Python and `boto3`.

## What is S3?

- **S**imple **S**torage **S**ervice
- Object storage service (not file system storage)
- Stores data as objects inside buckets
- Each object has: data, metadata, and a unique key

## Core Concepts

| Concept | Description |
|---------|-------------|
| Bucket | Top-level container (like a folder) |
| Object | A file stored in a bucket |
| Key | Unique identifier for an object within a bucket |
| Region | AWS region where the bucket lives |
| ACL | Access Control List (permissions) |

## Project Structure

```
.
├── README.md
├── requirements.txt
├── .env.example
├── .gitignore
└── src/
    ├── 01_create_bucket.py
    ├── 02_upload_object.py
    ├── 03_list_objects.py
    ├── 04_download_object.py
    ├── 05_delete_object.py
    └── 06_presigned_url.py
```

## Setup

1. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```

2. Configure AWS credentials:
   ```bash
   aws configure
   ```
   Or set environment variables in a `.env` file (see `.env.example`).

3. Run the scripts in order:
   ```bash
   python src/01_create_bucket.py
   python src/02_upload_object.py
   # ... etc
   ```

## Learning Checklist

- [ ] Create a bucket
- [ ] Upload an object
- [ ] List objects in a bucket
- [ ] Download an object
- [ ] Generate a presigned URL
- [ ] Delete an object
- [ ] Delete a bucket
- [ ] Explore bucket policies and ACLs
- [ ] Enable versioning on a bucket
- [ ] Set up lifecycle policies
