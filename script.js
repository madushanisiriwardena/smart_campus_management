

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
    { id: 1, date: "2024.03.17 02.30 - 2024.03.17 03.30", type: "Class Room", name: "A5", user: "Manusha", userType: "STUDENT", status: "Approved" },
    { id: 2, date: "2024.03.18 10.00 - 2024.03.18 11.00", type: "Resources", name: "Computer Lab", user: "John", userType: "LECTURE", status: "Rejected" },
    { id: 3, date: "2024.03.19 09.00 - 2024.03.19 10.00", type: "Equipment", name: "Projector", user: "Alice", userType: "STUDENT", status: "Pending" }
];

const reservationsStudent = [
    { id: 1, code: "C2", type: "Class Room", name: "E001", status: "Active" },
    { id: 1, code: "L2", type: "Resources", name: "Computer Lab", status: "Active" },
    { id: 1, code: "E2", type: "Equipment", name: "Projector", status: "Active" },
    { id: 1, code: "R2", type: "Resources", name: "Main Hall", status: "Active" },
    { id: 1, code: "E1", type: "Equipment", name: "White Screen", status: "Active" },
];

const resources = [
    { id: 1, code: "A2", name: "Computer Lab", maxCount: "150", status: "Active" },
    { id: 2, code: "E2", name: "Main Hall", maxCount: "500", status: "Active" },
    { id: 3, code: "L1", name: "Library", maxCount: "50", status: "Active" },
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
                    <button type="button" class="btn btn-success" onclick="approved(${res.id});">Approve</button>
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

    reservationsStudent.forEach((res, index) => {
        let row = `<tr>
                <th scope="row">${index + 1}</th>
                <td>${res.code}</td>
                <td>${res.type}</td>
                <td>${res.name}</td>
                <td id="status-${res.id}">${res.status}</td>
                <td class="action">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#p3"">Booking</button>
                </td>
            </tr>`;
        tableBody.innerHTML += row;
    });
}

function book(index) {
    if (index === 1) {
        Swal.fire({
            title: "Error!",
            text: "That time slot is not available in this facility.",
            icon: "error",
            confirmButtonColor: "#d33",
            confirmButtonText: "OK",
            backdrop: false, // Keeps background visible
        });
    } else if (index === 2) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to Book this?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Book it!",
            cancelButtonText: "No, cancel!",
            backdrop: false, // Prevents background dimming or hiding
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Booked!",
                    text: "The reservation has been Success.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    backdrop: false, // Keeps background visible
                });
                // Add your approval logic here if needed
            }
        });
    }
}