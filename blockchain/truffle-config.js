// truffle-config.js
const HDWalletProvider = require('@truffle/hdwallet-provider');
const mnemonic = 'fancy panda execute prefer discover company reduce car battle garlic scout tide'; // Replace with your MetaMask mnemonic
const ganacheUrl = 'http://127.0.0.1:8545'; // URL for Ganache

module.exports = {
  networks: {
    development: {
      host: "127.0.0.1",
      port: 8545,
      network_id: "5777", // Match any network id
      gas: 8000000,          // Increased gas limit
      gasPrice: 20000000000
    }
  },
  
  mocha: {
    // timeout: 100000
  },

  compilers: {
    solc: {
      version: "0.8.21",      // Fetch exact version from solc-bin (default: truffle's version)
      settings: {
        optimizer: {
          enabled: true,
          runs: 200
      // docker: true,        // Use "0.5.1" you've installed locally with docker (default: false)
      // settings: {          // See the solidity docs for advice about optimization and evmVersion
      //  optimizer: {
      //    enabled: false,
      //    runs: 200
      //  },
      //  evmVersion: "byzantium"
      // }
        }
      }
    }
  },

  // Truffle DB is currently disabled by default; to enable it, change enabled: false to enabled: true.
  // The default storage location can also be overridden by specifying the adapter settings, as shown in the commented code below.
  //
  // NOTE: It is not possible to migrate your contracts to truffle DB and you should
  // make a backup of your artifacts to a safe location before enabling this feature.
  //
  // After you backed up your artifacts you can utilize db by running migrate as follows:
  // $ truffle migrate --reset --compile-all
  //
  // db: {
  //   enabled: false,
  //   host: "127.0.0.1",
  //   adapter: {
  //     name: "indexeddb",
  //     settings: {
  //       directory: ".db"
  //     }
  //   }
  // }
};
