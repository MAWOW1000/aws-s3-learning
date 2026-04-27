import boto3
from botocore.exceptions import ClientError

def create_presigned_url(bucket_name, object_name, expiration=3600):
    """Generate a presigned URL to share an S3 object.

    expiration: Time in seconds for the presigned URL to remain valid (max 7 days)
    """
    s3_client = boto3.client('s3')
    try:
        url = s3_client.generate_presigned_url(
            'get_object',
            Params={'Bucket': bucket_name, 'Key': object_name},
            ExpiresIn=expiration
        )
        return url
    except ClientError as e:
        print(f"Error generating presigned URL: {e}")
        return None

if __name__ == "__main__":
    BUCKET_NAME = "my-learning-bucket-123"
    OBJECT_NAME = "hello_s3.txt"

    url = create_presigned_url(BUCKET_NAME, OBJECT_NAME, expiration=3600)
    if url:
        print("Presigned URL (valid for 1 hour):")
        print(url)
