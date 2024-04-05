const openModal = document.querySelector('.hero__cta');
const modal = document.querySelector('.modal');
const closeModal = document.querySelector('.modal__close');

closeModal.addEventListener('click', (e)=>{
    e.preventDefault();
    modal.classList.remove('modal--show');
});

//event listener
document.addEventListener('DOMContentLoaded', (event) => {
		
		let captchaImg = document.querySelector('#captcha_img');
		
		let captchaRefreshBtn = document.querySelector('#btn_captcha_refresh');

		captchaRefreshBtn.addEventListener('click', function(e){
			e.preventDefault();
			captchaImg.src = "core/captcha_image.php";
			document.querySelector('#captcha_num').value = "";
		});
		
	    let submitBtn = document.querySelector('#btn_form_submit');
		
		submitBtn.addEventListener('click', function(e){
			
			e.preventDefault();
			
			let serial = document.querySelector('#serial').value;
			let captcha_num = document.querySelector('#captcha_num').value;
			
			//validation
			if(captcha_num=='' || captcha_num.length != 4) {
				alert("Captcha length is not valid!");
				return false;
			}
			
			if(serial=='') {
				alert("Serial/Imei cannot be blank!");
				return false;
			}

			if(serial.length <10 || serial.length >20) {
				alert("Serial/Imei length is not valid!");
				return false;
			}

			// Example POST method implementation:
			async function postData(url = "", data = {}) {
			  // Default options are marked with *
			  const response = await fetch(url, {
				method: "POST", // *GET, POST, PUT, DELETE, etc.
				mode: "cors", // no-cors, *cors, same-origin
				cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
				credentials: "same-origin", // include, *same-origin, omit
				headers: {
				  "Content-Type": "application/json",
				  // 'Content-Type': 'application/x-www-form-urlencoded',
				},
				redirect: "follow", // manual, *follow, error
				referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
				body: JSON.stringify(data), // body data type must match "Content-Type" header
			  });
			  
			  return response.json(); // parses JSON response into native JavaScript objects
			}
			
			let dataPost = {"serial": serial, "captcha_num": captcha_num};

			postData("core/process_form.php", dataPost ).then( (data) => {
				
				  if (data.status == "success") {

					    let modalBody = document.getElementById('modal_body');
                        modalBody.innerHTML = data.imei_status;
            
						e.preventDefault();
						//Show the modal
						modal.classList.add('modal--show');
					  
				  } else {
					  alert("Alert! " + data.details);
				  }
				  
                  //Refresh all
                  captchaImg.src = "core/captcha_image.php";
				  document.querySelector('#captcha_num').value = "";
                  //document.querySelector('#serial').value = "";			
                 
				}
		    ).catch((error) => {
				  alert("Unknow Error!");
				  captchaImg.src = "core/captcha_image.php";
				  console.log('Error-' + error); 
			});

		});	
		
	});