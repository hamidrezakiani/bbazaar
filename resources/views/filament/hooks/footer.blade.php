<script>
    var scrollToSection = function (event) {
        setTimeout(() => {
            const activeSidebarItem = document.querySelector('.fi-sidebar-item-active');
            const sidebarWrapper = document.querySelector('.fi-sidebar-nav')

            if (
                typeof (sidebarWrapper) != 'undefined' && sidebarWrapper != null &&
                typeof (activeSidebarItem) != 'undefined' && activeSidebarItem != null
            ) {
                sidebarWrapper.style.scrollBehavior = 'smooth';
                sidebarWrapper.scrollTo(0, activeSidebarItem.offsetTop - 250)
            }

        }, 1)
    };

    document.addEventListener('livewire:navigated', scrollToSection);
    document.addEventListener('DOMContentLoaded', scrollToSection);
</script>

<script>
    function printPageArea(areaID) {
        let printContent = document.getElementById(areaID);
        let WinPrint = window.open('', '', 'width=900,height=650');
        WinPrint.document.write(
            '<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&amp;display=swap" rel="stylesheet">'
        );
        WinPrint.document.write(
            '<link rel="stylesheet" href="https://bbazar-update.test/css/filament/forms/forms.css?v=3.1.34.0" type="text/css" />'
        );
        WinPrint.document.write(
            '<link rel="stylesheet" href="https://bbazar-update.test/build/assets/support.css?v=3.1.34.0" type="text/css" />'
        );
        WinPrint.document.write('<link rel="preload" as="style" href="https://bbazar-update.test/build/assets/theme-252fca9b.css"/>')
        WinPrint.document.write('<link rel="stylesheet" href="https://bbazar-update.test/build/assets/theme-252fca9b.css"/>')
        WinPrint.document.write('<link rel="preconnect" href="https://fonts.googleapis.com">')
        WinPrint.document.write('<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>')
        WinPrint.document.write('<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet" />')
        WinPrint.document.write('<style>body{font-family: "Rubik",sans-serif !important} </style>')
        WinPrint.document.write(printContent.innerHTML);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
    }
</script>
