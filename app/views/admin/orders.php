<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body>
<?php
    require __DIR__ . '/../adminNavbar.php';
    ?>
    <div class="container-fluid">
        <h1>Manage Orders</h1>
        <button class="btn btn-success" type="button" style="float: right; margin-left: 10px" onclick="exportToCsv()">Export Table</button>
        <button class="btn btn-primary" type="button" style="float: right" onclick="window.location.href = '/admin/generateApiKey' ">Generate Key</button>
        <section>
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th scope="col">
                        <input type="checkbox" id="orderIdCheckbox" class="headerCheckbox" checked>   
                        Order ID</th>
                        <th scope="col">
                        <input type="checkbox" id="paymentIdCheckbox" class="headerCheckbox" checked>
                        Payment ID</th>
                        <th scope="col">
                        <input type="checkbox" id="invoiceNumberCheckbox" class="headerCheckbox" checked>    
                        Invoice Number</th>
                        <th scope="col">
                        <input type="checkbox" id="invoiceDateCheckbox" class="headerCheckbox" checked>    
                        Invoice Date</th>
                        <th scope="col">
                        <input type="checkbox" id="productsCheckbox" class="headerCheckbox" checked>    
                        Products</th>
                        <th scope="col">
                        <input type="checkbox" id="paymentStatusCheckbox" class="headerCheckbox" checked>    
                        Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order){?>
                    <tr>
                        <th scope="row"><?php echo $order->getOrderId()?></th>
                        <td><?php echo $order->getPaymentId()?></td>
                        <td><?php echo $order->getInvoiceNumber()?></td>
                        <td><?php echo $order->getInvoiceDate()?></td>
                        <td><?php echo $order->getListProductId()?></td>
                        <td><select name="paymentStatuses" id="<?php echo "paymentStatus " . $order->getOrderId()?>" onchange="updateField(<?php echo $order->getOrderId()?>)">
                                <option value="Paid" <?php if($order->getPaymentStatus() == "Paid") echo 'selected'?>>Paid</option>
                                <option value="Pending" <?php if($order->getPaymentStatus() == "Pending") echo 'selected'?>>Pending</option>
                                <option value="Cancelled" <?php if($order->getPaymentStatus() == "Cancelled") echo 'selected'?>>Cancelled</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </div>
    <script>
        function exportToCsv(){
            var table = document.getElementById('ordersTable');
            var checkboxes = document.getElementsByClassName('headerCheckbox');
            var selectedColumns = [];

            // Get the selected columns
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                var columnIndex = checkboxes[i].parentNode.cellIndex;
                selectedColumns.push(columnIndex);
                }
            }

            var csv = [];

            // Iterate through each table row
            for (var i = 0; i < table.rows.length; i++) {
                var row = [];

                // Iterate through each selected column in the row
                for (var j = 0; j < selectedColumns.length; j++) {
                var columnIndex = selectedColumns[j];
                var cell = table.rows[i].cells[columnIndex];
                var cellValue;

                if (columnIndex === table.rows[i].cells.length - 1 && i > 0) {
                    // Handle select element in the last column for rows after the first row
                    cellValue = cell.querySelector('select').value;
                } else {
                    cellValue = cell.innerText;
                }

                // Split the cell value into characters and add them individually as CSV cells
                row.push(cellValue.trim());
                }

                // Join the row with commas and add to the CSV array
                csv.push(row.join(';'));
            }

            // Join all rows with newlines and create the CSV content
            var csvContent = csv.join('\n');

            console.log(csvContent);

            // Download the CSV file
            var encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csvContent);
            var link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'order_data ' + Date() + '.csv');
            document.body.appendChild(link);
            link.click();
        }

        function updateField(id){
            const paymentSelect = document.getElementById("paymentStatus " + id);

            const orderPaymentData = {
                "payment_status": paymentSelect.value
            };

            fetch("http://localhost/api/orders?id=" + id,{
                method: 'PATCH',
                headers:{
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(orderPaymentData),
            })
            .then((response) => response.json())
            .then((data)=> console.log(data))
            .catch((error)=> console.error(error));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
</body>
</html>