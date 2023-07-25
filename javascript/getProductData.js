// Pseudocode example
// This asynchronous function retrieves product data of a specific type from two different URLs: "https://sample.com/products" and "https://sample.com/producttypes".
// It uses the fetchData function to make parallel asynchronous requests to both URLs and filters the products by the given productType.
// The filtered products and the productTypes response are logged to the console, and the filtered products are returned as the result.
// Any errors that occur during data retrieval are caught, and an error message is logged before re-throwing the error.

async function fetchData(url) {
  try {
    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`Request failed with status ${response.status}`);
    }

    return await response.json();
  } catch (error) {
    console.error(`Error fetching data from ${url}:`, error.message);
    throw error;
  }
}

async function getProductData(productType) {
  try {
    const productsUrl = "https://sample.com/products";
    const productTypesUrl = "https://sample.com/producttypes";

    const [productsResponse, productTypesResponse] = await Promise.all([
      fetchData(productsUrl),
      fetchData(productTypesUrl),
    ]);

    const filteredProducts = productsResponse.filter(
      (product) => product.type === productType
    );

    console.log("Filtered Products:", filteredProducts);
    console.log("Product Types:", productTypesResponse);

    return filteredProducts;
  } catch (error) {
    console.error("Error during data retrieval:", error);
    throw error;
  }
}

// The following code is intended for setting up a unit test, and has been left in the file temporarily for that purpose.
// It should not be considered as a final implementation, as it may contain unoptimized or incomplete logic.

// Mock data fetchData function
// async function fetchData(url) {
//   if (url === "https://sample.com/products") {
//     return [
//       { id: 1, type: 'wills', name: 'Basic Online Will' },
//       { id: 2, type: 'trusts', name: 'Family Trusts' },
//       { id: 3, type: 'wills', name: 'Comprehensive Online Will' },
//     ];
//   } else if (url === "https://sample.com/producttypes") {
//     return ['wills', 'trusts'];
//   } else {
//     throw new Error('Unknown URL');
//   }
// }

// Export to use in unit tests
// module.export = {
//   getProductData,
//   fetchData
// }
