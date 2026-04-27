import boto3
from botocore.exceptions import ClientError

def delete_object(bucket_name, object_name):
    """Delete an object from an S3 bucket."""
    s3_client = boto3.client('s3')
    try:
        s3_client.delete_object(Bucket=bucket_name, Key=object_name)
        print(f"Deleted object '{object_name}' from bucket '{bucket_name}'.")
    except ClientError as e:
        print(f"Error deleting object: {e}")

def delete_bucket(bucket_name):
    """Delete an empty S3 bucket."""
    s3_client = boto3.client('s3')
    try:
        s3_client.delete_bucket(Bucket=bucket_name)
        print(f"Deleted bucket '{bucket_name}'.")
    except ClientError as e:
        print(f"Error deleting bucket: {e}")

if __name__ == "__main__":
    BUCKET_NAME = "my-learning-bucket-123"
    OBJECT_NAME = "hello_s3.txt"

    # First delete the object, then the bucket (bucket must be empty)
    delete_object(BUCKET_NAME, OBJECT_NAME)
    # delete_bucket(BUCKET_NAME)  # Uncomment after objects are removed
