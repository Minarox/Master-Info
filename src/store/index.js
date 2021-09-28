import { createStore } from 'vuex'
import dataset from '../assets/dataset.json'
import accounts from '../assets/accounts.json'

let categories = []
for (const data of dataset) {
  categories.push(data['title_category'])
}

export default createStore({
  state: {
    Dataset: dataset,
    Categories: categories,
    Accounts: accounts
  }
})
