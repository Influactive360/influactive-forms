function i(r,t,s){const e=new XMLHttpRequest,n=new FormData(t);n.append("action","send_email"),s&&n.append("recaptcha_response",s),e.open("POST",ajaxObject.ajaxurl,!0),e.onload=function(){const a=r;if(e.status===200){const o=JSON.parse(e.responseText);o.data?(a.textContent=o.data.message,t.reset()):a.textContent=o.data.message}else a.textContent="An error occurred with the AJAX request"},e.send(n)}document.addEventListener("DOMContentLoaded",()=>{document.querySelectorAll(".influactive-form").forEach(t=>{t.parentElement.parentElement.parentElement.classList.contains("influactive-modal-form-brochure")||t.addEventListener("submit",s=>{s.preventDefault();const e=t.querySelector(".influactive-form-message"),n=t.querySelector('input[name="recaptcha_site_key"]');if(n&&grecaptcha){const c=n.value;grecaptcha.ready(()=>{grecaptcha.execute(c,{action:"submit"}).then(a=>{i(e,t,a),setTimeout(()=>{e.textContent=""},5e3)})})}else i(e,t,null),setTimeout(()=>{e.textContent=""},5e3)})})});