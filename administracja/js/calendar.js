const carriers = [
    "",
    "Rokbus (Rokietnica)",
    "ZKP Suchy Las",
    "Transkom (Murowana Goślina, Czerwonak)",
    "PUK Komorniki",
    "PUK Dopiewo",
    "Translub (Luboń)",
    "Marco Polo",
    "PKS Poznań"
]

function showCalendar() {
    let x = document.getElementById("month").selectedIndex,
        y = document.getElementById("month").options,
        month = y[x].index + 1,
        year = document.getElementById("year").value,
        days = daysInMonth(month, year),
        calendar = ""

    function daysInMonth(month, year) {
        return new Date(year, month, 0).getDate()
    }

    function showSelect(z) {
        let selects = ""
        function showOptions() {
            options = ""
            for (let b in carriers) {
                options += "<option>" + carriers[b] + "</option>"
            }
            return options
        }

        for (let a = 0; a < 3; a++) {
            selects += "<td><select name='" + z + "shift" + (a+1) + "'>" + showOptions() + "</select></td>"
        }
        return selects
    }

    for (let i = 1; i <= days; i++) {
        calendar += "<tr class='teams'><td>" + i + "</td>" + showSelect(i) + "</tr>"
    }
    document.getElementById("days").innerHTML = calendar

    input = "<input type='hidden' name='days' value='" + days + "'>"
    document.getElementById("daysSum").innerHTML = input
}
