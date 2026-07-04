import { db } from "./firebase-config.js";

import {
    collection,
    getDocs,
    doc,
    getDoc,
    addDoc,
    updateDoc,
    deleteDoc
} from "https://www.gstatic.com/firebasejs/12.0.0/firebase-firestore.js";

const beritaRef = collection(db,"berita");

export async function getAllBerita(){

    const snapshot = await getDocs(beritaRef);

    let data=[];

    snapshot.forEach((docu)=>{

        data.push({
            id:docu.id,
            ...docu.data()
        });

    });

    return data;

}

export async function getBeritaById(id){

    const snapshot=await getDoc(doc(db,"berita",id));

    if(snapshot.exists()){

        return{
            id:snapshot.id,
            ...snapshot.data()
        };

    }

    return null;

}

export async function addBerita(data){

    await addDoc(beritaRef,data);

}

export async function updateBerita(id,data){

    await updateDoc(doc(db,"berita",id),data);

}

export async function deleteBerita(id){

    await deleteDoc(doc(db,"berita",id));

}