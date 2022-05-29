"use strict";
const axios = require("axios").default;
const api_url = "https://petfeeder.minarox.fr/api";
const headers = {
  headers: {
    "Content-Type": "application/json",
  },
};

export const API = {
  // Passage
  getPassages,
  addPassage,
  getPassage,

  // Food
  getFoods,
  addFood,
  getFood,
};

function getPassages() {
  return axios.get(api_url + "/passages", headers).then((response) => {
    return response["data"];
  });
}

function getPassage(passage_id) {
  return axios
    .get(api_url + "/passages/" + passage_id, headers)
    .then((response) => {
      return response["data"];
    });
}

function addPassage() {
  return axios.post(api_url + "/passages", headers).then((response) => {
    return response["data"];
  });
}

function getFoods() {
  return axios.get(api_url + "/food", headers).then((response) => {
    return response["data"];
  });
}

function getFood(food_id) {
  return axios.get(api_url + "/food/" + food_id, headers).then((response) => {
    return response["data"];
  });
}

function addFood() {
  return axios.post(api_url + "/food", headers).then((response) => {
    return response["data"];
  });
}
