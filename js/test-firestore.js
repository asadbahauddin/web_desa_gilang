import { db } from "./firebase-config.js";

import {
    collection,
    getDocs
} from "https://www.gstatic.com/firebasejs/12.5.0/firebase-firestore.js";

async function test() {
    const querySnapshot = await getDocs(collection(db, "berita"));

    querySnapshot.forEach((doc) => {
        console.log(doc.id, doc.data());
    });
}

test();