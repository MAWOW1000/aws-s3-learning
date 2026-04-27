import boto3
from botocore.exceptions import ClientError

def create_bucket(bucket_name, region=None):
    """Create an S3 bucket in a specified region.

    If a region is not specified, the bucket is created in the S3 default
    region (us-east-1).
    """
    try:
        s3_client = boto3.client('s3')
        if region is None:
            s3_client.create_bucket(Bucket=bucket_name)
        else:
            location = {'LocationConstraint': region}
            s3_client.create_bucket(
                Bucket=bucket_name,
                CreateBucketConfiguration=location
            )
        print(f"Bucket '{bucket_name}' created successfully.")
    except ClientError as e:
        print(f"Error creating bucket: {e}")

if __name__ == "__main__":
    BUCKET_NAME = "my-learning-bucket-123"  # change this to something globally unique
    REGION = "us-east-1"
    create_bucket(BUCKET_NAME, REGION)
