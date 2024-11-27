import { DynamoDBClient } from "@aws-sdk/client-dynamodb";
import {
  DynamoDBDocumentClient,
  ScanCommand,
  GetCommand,
} from "@aws-sdk/lib-dynamodb";

// Initialize DynamoDB client and document client
const client = new DynamoDBClient({});
const dynamo = DynamoDBDocumentClient.from(client);

const tableName = "band";

export const handler = async (event, context) => {
  let body;
  let statusCode = 200;
  const headers = {
    "Content-Type": "application/json",
  };

  try {
    switch (event.routeKey) {
      // Fetch a specific band by band_id
      case "GET /items/{band_id}":
        const bandId = event.pathParameters.band_id;
        const bandData = await dynamo.send(
          new GetCommand({
            TableName: tableName,
            Key: { band_id: bandId },
          })
        );
        body = bandData.Item || {}; // Return empty object if no item found
        break;

      // Fetch all bands and combine with cities
      case "GET /items":
        // Fetch data from the "band" table
        const bandDataResponse = await dynamo.send(
          new ScanCommand({ TableName: "band" })
        );
        const bands = bandDataResponse.Items;

        // Fetch data from the "city" table
        const cityDataResponse = await dynamo.send(
          new ScanCommand({ TableName: "city" })
        );
        const cities = cityDataResponse.Items;

        // Fetch data from the "country" table
        const countryDataResponse = await dynamo.send(
          new ScanCommand({ TableName: "country" })
        );
        const countries = countryDataResponse.Items;

        // Combine data from both tables
        const combinedData = bands.map((band) => {
          const cityCode = band.city_code; // Get the city_code from the band
          
          // Find the matching city in the city table based on city_code
          const matchingCity = cities.find(
            (city) => city.city_code === cityCode
          );

          const countryCode = band.country_code; // Get the city_code from the band 

          // Find the matching city in the city table based on city_code
          const matchingCountry = countries.find(
            (country) => country.country_code === countryCode
          );

          // If a matching city is found, merge band and city data
            return {
              band_id: band.band_id,  // Accessing band_id as a number
              band_name: band.band_name, 
              city_name: (matchingCity) ? matchingCity.city_name:null,
              country_name: (matchingCountry) ? matchingCountry.country_name:null,
             // country_name: null,  // Accessing country_code as a number
              formed_year: band.formed_year,    // Accessing formed_year as a number
            };
        });

        body = combinedData; // Set the combined data to the response body
        break;

      default:
        throw new Error(`Unsupported route: "${event.routeKey}"`);
    }
  } catch (err) {
    // Handle errors (set status code and error message)
    statusCode = 500;
    body = { message: "Internal Server Error", error: err.message };
  }
console.log("body:", body);
  // Return the response object
  return {
    statusCode,
    body: JSON.stringify(body), // Convert body to a JSON string
    headers,
  };
};