import boto3
from botocore.exceptions import ClientError

def list_objects(bucket_name):
    """List all objects in an S3 bucket."""
    s3_client = boto3.client('s3')
    try:
        response = s3_client.list_objects_v2(Bucket=bucket_name)
        if 'Contents' in response:
            print(f"Objects in bucket '{bucket_name}':")
            for obj in response['Contents']:
                print(f"  - {obj['Key']} ({obj['Size']} bytes)")
        else:
            print(f"Bucket '{bucket_name}' is empty.")
    except ClientError as e:
        print(f"Error listing objects: {e}")

if __name__ == "__main__":
    BUCKET_NAME = "my-learning-bucket-123"
    list_objects(BUCKET_NAME)
