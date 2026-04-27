import boto3
from botocore.exceptions import ClientError

def upload_file(file_name, bucket, object_name=None):
    """Upload a file to an S3 bucket.

    file_name: File to upload
    bucket: Bucket to upload to
    object_name: S3 object name. If not specified, file_name is used
    """
    if object_name is None:
        object_name = file_name

    s3_client = boto3.client('s3')
    try:
        s3_client.upload_file(file_name, bucket, object_name)
        print(f"Uploaded '{file_name}' to bucket '{bucket}' as '{object_name}'.")
    except ClientError as e:
        print(f"Error uploading file: {e}")

if __name__ == "__main__":
    # Create a sample file to upload
    sample_text = "Hello, S3! This is my first uploaded object."
    with open("hello_s3.txt", "w") as f:
        f.write(sample_text)

    upload_file("hello_s3.txt", "my-learning-bucket-123", "hello_s3.txt")
