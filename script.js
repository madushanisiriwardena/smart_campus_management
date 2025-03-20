

function approved(index) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to approve this?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, approve it!",
        cancelButtonText: "No, cancel!",
        backdrop: false, // Prevents background dimming or hiding
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Approved!",
                text: "The reservation has been approved.",
                icon: "success",
                backdrop: false, // Keeps background visible
            });
            // Add your approval logic here (e.g., updating the database)
        }
    });
}

function reject(index) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to Reject this?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "rgb(107 107 107)",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Reject it!",
        cancelButtonText: "No, cancel!",
        backdrop: false, // Prevents background dimming or hiding
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Reject",
                text: "You cancelled the operation.",
                icon: "error",
                backdrop: false, // Keeps background visible
            });
        }
    });
}

const reservations = [
    { id: 1, date: "2024.03.17 02.30 - 2024.03.17 03.30", type: "Class Room", name: "A5", user: "Manusha", userType: "STUDENT", status: "Pending" },
    { id: 2, date: "2024.03.18 10.00 - 2024.03.18 11.00", type: "Lab", name: "B2", user: "John", userType: "TEACHER", status: "Approved" },
    { id: 3, date: "2024.03.19 09.00 - 2024.03.19 10.00", type: "Hall", name: "C1", user: "Alice", userType: "STAFF", status: "Rejected" }
];

const resources = [
    { id: 1, code: "A2", name: "A5", maxCount: "Manusha", status: "Pending" },
];


function loadTableData() {
    let tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    reservations.forEach((res, index) => {
        let row = `<tr>
                <th scope="row">${index + 1}</th>
                <td>${res.date}</td>
                <td>${res.type}</td>
                <td>${res.name}</td>
                <td>${res.user}</td>
                <td>${res.userType}</td>
                <td id="status-${res.id}">${res.status}</td>
                <td class="action">
                    <button type="button" class="btn btn-success" onclick="approve(${res.id});">Approve</button>
                    <button type="button" class="btn btn-danger" onclick="reject(${res.id});">Reject</button>
                </td>
            </tr>`;
        tableBody.innerHTML += row;
    });
}

function loadResourcesData() {
    let tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    resources.forEach((res, index) => {
        let row = `<tr>
                <th scope="row">${index + 1}</th>
                <td>${res.code}</td>
                <td>${res.name}</td>
                <td>${res.maxCount}</td>
                <td id="status-${res.id}">${res.status}</td>
                <td class="action">
                    <button type="button" class="btn btn-success" onclick="approve(${res.id});">Edit</button>
                </td>
            </tr>`;
        tableBody.innerHTML += row;
    });
}


function loadSReservationData() {
    let tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = ""; // Clear existing rows

    reservations.forEach((res, index) => {
        let row = `<tr>
                <th scope="row">${index + 1}</th>
                <td>${res.date}</td>
                <td>${res.type}</td>
                <td>${res.name}</td>
                <td>${res.user}</td>
                <td>${res.userType}</td>
                <td id="status-${res.id}">${res.status}</td>
                <td class="action">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#p3"">Booking</button>
                </td>
            </tr>`;
        tableBody.innerHTML += row;
    });
}
