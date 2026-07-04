/* ==========================================
   NAVBAR DESA GILANG
========================================== */

document.addEventListener("DOMContentLoaded", () => {

    const toggle = document.getElementById("navToggle");
    const menu = document.getElementById("navMenu");

    // ===========================
    // Mobile Menu
    // ===========================

    if(toggle && menu){

        toggle.addEventListener("click",()=>{

            menu.classList.toggle("active");

            toggle.classList.toggle("active");

            const expanded = toggle.getAttribute("aria-expanded")==="true";

            toggle.setAttribute("aria-expanded",!expanded);

        });

    }

    // ===========================
    // Dropdown
    // ===========================

    const dropdowns=document.querySelectorAll("[data-dropdown]");

    dropdowns.forEach(dropdown=>{

        const button=dropdown.querySelector("button");

        button.addEventListener("click",(e)=>{

            e.preventDefault();

            dropdowns.forEach(item=>{

                if(item!==dropdown){

                    item.classList.remove("open");

                    item.querySelector("button")
                    ?.setAttribute("aria-expanded","false");

                }

            });

            dropdown.classList.toggle("open");

            button.setAttribute(
                "aria-expanded",
                dropdown.classList.contains("open")
            );

        });

    });

    // ===========================
    // Klik di luar dropdown
    // ===========================

    document.addEventListener("click",(e)=>{

        dropdowns.forEach(dropdown=>{

            if(!dropdown.contains(e.target)){

                dropdown.classList.remove("open");

                dropdown.querySelector("button")
                ?.setAttribute("aria-expanded","false");

            }

        });

    });

});