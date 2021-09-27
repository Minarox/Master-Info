import { createStore } from 'vuex'
import dataset from '../assets/dataset.json'

let categories = []
for (const data of dataset) {
  categories.push(data['title_category'])
}

export default createStore({
  state: {
    Dataset: dataset,
    Categories: categories
  }
})
