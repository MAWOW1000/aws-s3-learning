import boto3
from botocore.exceptions import ClientError

def download_file(bucket, object_name, file_name):
    """Download an object from an S3 bucket."""
    s3_client = boto3.client('s3')
    try:
        s3_client.download_file(bucket, object_name, file_name)
        print(f"Downloaded '{object_name}' from bucket '{bucket}' to '{file_name}'.")
    except ClientError as e:
        print(f"Error downloading file: {e}")

if __name__ == "__main__":
    BUCKET_NAME = "my-learning-bucket-123"
    OBJECT_NAME = "hello_s3.txt"
    LOCAL_FILE = "downloaded_hello_s3.txt"

    download_file(BUCKET_NAME, OBJECT_NAME, LOCAL_FILE)

    # Read and print the downloaded file
    try:
        with open(LOCAL_FILE, "r") as f:
            print("\nFile contents:")
            print(f.read())
    except FileNotFoundError:
        print("Download failed - file not found.")
