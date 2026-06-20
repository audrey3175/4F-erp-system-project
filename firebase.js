// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyB9v8xtRNqgOyqhaicTZcP1iBvPQqK1rzA",
  authDomain: "foodsycn-erp.firebaseapp.com",
  projectId: "foodsycn-erp",
  storageBucket: "foodsycn-erp.firebasestorage.app",
  messagingSenderId: "863332048699",
  appId: "1:863332048699:web:cec060af2178eb4f6aa11e",
  measurementId: "G-RX2DT8D522"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);