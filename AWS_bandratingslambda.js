import { DynamoDBClient } from "@aws-sdk/client-dynamodb";
import {
  DynamoDBDocumentClient,
  ScanCommand,
  PutCommand,
  GetCommand,
  DeleteCommand,
  QueryCommand
} from "@aws-sdk/lib-dynamodb";

// Initialize DynamoDB client
const client = new DynamoDBClient({});
const dynamo = DynamoDBDocumentClient.from(client);

const tableName = "band_rating";  // Your DynamoDB table name

// Function to check if band rating exists or get next band_rating_id
async function getItemBandRatingId(user_id, band_id) { 
  try {
    // Define the Query parameters
    const queryCommand = new QueryCommand({
      TableName: tableName,
      IndexName: "user_id-band_id-index",
      KeyConditionExpression: "user_id = :user_id AND band_id = :band_id",  // Compare user_id and band_id
      ExpressionAttributeValues: {
        ":user_id": user_id,  // Partition key
        ":band_id": band_id,  // Sort key
      },
    });

    // Execute the query command
    const result = await dynamo.send(queryCommand);

    // Check if any matching items are returned
    if (result.Items && result.Items.length > 0) {
      return result.Items[0].band_rating_id;
    } else { 
      //Get higest band_rating_id and increment by 1
      const lastBandRatingId = await getHighestBandRatingId();
      const newBandRatingId = parseInt(lastBandRatingId, 10) + 1;
      return newBandRatingId;   
    } 
  } catch (error) {
    console.error("Error comparing item:", error);
    throw new Error("Could not compare item: " + error.message);
  }
}


async function getHighestBandRatingId() {
  try {
    // Define the Scan parameters to get all items from the table
    const scanCommand = new ScanCommand({
      TableName: tableName,
      ProjectionExpression: "band_rating_id",  // Only retrieve band_rating_id for efficiency
    });

    // Execute the Scan command
    const result = await dynamo.send(scanCommand);

    // Check if any items were returned
    if (result.Items && result.Items.length > 0) {
      // Find the highest band_rating_id from the result
        const highestBandRatingId = result.Items.reduce((max, item) => {
        const bandRatingId = parseInt(item.band_rating_id, 10); // Ensure it's a number
        return bandRatingId > max ? bandRatingId : max;
      }, 0);  // Start with 0 if no items exist

      console.log("Highest band_rating_id:", highestBandRatingId);
      return highestBandRatingId;
    } else { 
      return 1; 
    }

  } catch (error) {
    console.error("Error scanning DynamoDB:", error);
    throw new Error("Could not retrieve the highest band_rating_id: " + error.message);
  }
}


// AWS API POST CALLS
export const handler = async (event, context) => {
  let body;
  let statusCode = 200;
  const headers = {
    "Content-Type": "application/json",
  };
  try {
    switch (event.routeKey) {
      case "DELETE /items/{band_rating_id}":
        // DELETE operation
        await dynamo.send(
          new DeleteCommand({
            TableName: tableName,
            Key: {
              band_rating_id: parseInt(event.pathParameters.band_rating_id, 10)  ,  // Assuming band_rating_id is a number
            },
          })
        );
        body = `Deleted item with band_rating_id: ${event.pathParameters.band_rating_id}`;
        break;

      case "GET /items/{band_rating_id}":
        // GET operation (fetch item by band_rating_id)
        body = await dynamo.send(
          new GetCommand({
            TableName: tableName,
            Key: {
              band_rating_id: event.pathParameters.band_rating_id ,  // Assuming band_rating_id is a number
            },
          })
        );
        body = body.Item;
        break;

      case "GET /items":
        // GET operation (fetch all items)
        body = await dynamo.send(
          new ScanCommand({ TableName: tableName })
        );
        body = body.Items;
        break;

      case "PUT /items":
        // PUT operation (create new band rating)
        const requestJSON = JSON.parse(event.body);
        bandratingid:requestJSON.user_id;

       const nextBandRatingId = await getItemBandRatingId(requestJSON.user_id, requestJSON.band_id);
       console.log(nextBandRatingId);
        // Perform the PUT operation (insert new rating)
        await dynamo.send(
          new PutCommand({
            TableName: tableName,
            Item: {
              band_rating_id: nextBandRatingId,
              user_id: requestJSON.user_id,
              band_id: requestJSON.band_id, 
              modified: requestJSON.modified, 
              star_rating: requestJSON.star_rating,  
            },
          })
        );
        break;

      default:
        throw new Error(`Unsupported route: "${event.routeKey}"`);
    }
  } catch (err) {
    statusCode = 400;
    body = err.message;
  } finally {
    body = JSON.stringify(body);
  }

  return {
    statusCode,
    body,
    headers,
  };
};