<?php

include_once 'api/config/Config.php';

$payload = file_get_contents("payload.json");

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- <meta http-equiv="Content-Security-Policy" content="form-action https://www.sandbox.paypal.com/checkoutnow" /> -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
		<title>PayPal REST API - PHP Example Page</title>
	</head>
	<body>
		<script src="https://www.paypal.com/sdk/js?client-id=<?=PAYPAL_CREDENTIALS[PAYPAL_ENVIRONMENT]['client_id']?>&components=card-fields&currency=EUR&intent=capture&commit=false"></script>
		<h1>PayPal Advanced Checkout - PHP Example Page</h1>
		<div id="checkout-form">
			<div id="card-name-field-container"></div>
			<div id="card-number-field-container"></div>
			<div id="card-expiry-field-container"></div>
			<div id="card-cvv-field-container"></div>
			<!-- <div>
				<label for="card-billing-address-line-1">Billing Address</label><br/>
				<input type="text" id="card-billing-address-line-1" name="card-billing-address-line-1" autocomplete="off" placeholder="Address line 1">
			</div>
			<div>
				<input type="text" id="card-billing-address-line-2" name="card-billing-address-line-2" autocomplete="off" placeholder="Address line 2">
			</div>
			<div>
				<input type="text" id="card-billing-address-admin-area-line-1" name="card-billing-address-admin-area-line-1" autocomplete="off" placeholder="Admin area line 1">
			</div>
			<div>
				<input type="text" id="card-billing-address-admin-area-line-2" name="card-billing-address-admin-area-line-2" autocomplete="off" placeholder="Admin area line 2">
			</div>
			<div>
				<input type="text" id="card-billing-address-country-code" name="card-billing-address-country-code" autocomplete="off" placeholder="Country code">
			</div>
			<div>
				<input type="text" id="card-billing-address-postal-code" name="card-billing-address-postal-code" autocomplete="off" placeholder="Postal/zip code">
			</div> -->
			<button id="card-field-submit-button" type="button">Pay with Card</button>
		</div>
		<hr/>
		<div id="payload-container"></div>
		<div id="response-container"></div>
		<script>
			function writeResponse (containerTitle, summaryTitle, content) {
				const container = document.getElementById(containerTitle);
				const details = document.createElement("details");
				const summary = document.createElement("summary");
				summary.innerHTML = summaryTitle;
				const pre = document.createElement("pre");
				pre.innerHTML = '<p>'+JSON.stringify(content, null, 2)+'</p>';
				const hr = document.createElement("hr");
				container.appendChild(details);
				details.appendChild(summary);
				details.appendChild(pre);
				container.appendChild(hr);
			};

			const cardField = paypal.CardFields({
				createOrder: function (data) {
					let formData = new FormData();
					formData.append("payload", JSON.stringify(<?= $payload ?>));
					writeResponse("payload-container", "Payload", <?= $payload ?>);
					console.log("payload: ", <?= $payload ?>);
					return fetch("api/createOrder.php", {
						method: "POST",
						body: formData,
					}).then((res) => {
						return res.json();
					}).then((orderData) => {
						if (orderData.response?.id) {
							writeResponse("response-container", "Create Order Response - "+orderData.status, orderData.response);
							console.log("createOrder: ", orderData.response);
							return orderData.response?.id;
						} else {
							writeResponse("response-container", "Create ERROR - "+orderData.status, orderData.response);
							console.error("createOrder: ", orderData.response);
						}
					});
				},
				onApprove: function (data) {
					const { orderID } = data;
					writeResponse("response-container", "onApprove - input", data);
					console.log("onApprove - input: ", data);
					return fetch("api/capturePaymentForOrder.php?id="+orderID, {
						method: "POST",
					}).then((res) => {
						return res.json();
					}).then((orderData) => {
						if (orderData.response?.purchase_units?.[0]?.payments?.captures?.[0]?.status === "COMPLETED" || orderData.response?.purchase_units?.[0]?.payments?.captures?.[0]?.status === "CREATED") {
							writeResponse("response-container", "Capture Payment For Order Response - "+orderData.status, orderData.response);
							console.log("onApprove: ", orderData.response);
						} else {
							writeResponse("response-container", "Capture ERROR - "+orderData.status, orderData.response);
							console.error("onApprove: ", orderData.response);
						}
					});
				},
				onError: function (error) {
					writeResponse("response-container", "ERROR", error);
					console.log("error", JSON.stringify(error));
				},
			});

			if (cardField.isEligible()) {
				const nameField = cardField.NameField();
				nameField.render("#card-name-field-container");
				const numberField = cardField.NumberField();
				numberField.render("#card-number-field-container");
				const cvvField = cardField.CVVField();
				cvvField.render("#card-cvv-field-container");
				const expiryField = cardField.ExpiryField();
				expiryField.render("#card-expiry-field-container");
				document.getElementById("card-field-submit-button").addEventListener("click", () => {
					cardField.submit({
						// billingAddress: {
						// 	addressLine1: document.getElementById("card-billing-address-line-1").value,
						// 	addressLine2: document.getElementById("card-billing-address-line-2").value,
						// 	adminArea1: document.getElementById("card-billing-address-admin-area-line-1").value,
						// 	adminArea2: document.getElementById("card-billing-address-admin-area-line-2").value,
						// 	countryCode: document.getElementById("card-billing-address-country-code").value,
						// 	postalCode: document.getElementById("card-billing-address-postal-code").value
						// }
					}).catch((error) => {
						console.log("submit");
					});
				});
			};
		</script>
	</body>
</html>