import { Component, ViewChild } from '@angular/core';
import { FormControl, FormGroup, NgForm } from '@angular/forms';
import { ShowFlightoffersComponent } from '../show-flightoffers/show-flightoffers.component';
import { FlightResponses } from '../_models/Flights/FlightResponses';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightService } from '../_services/flight.service';
import { CarrierCodesResponse } from '../_models/Flights/CarrierCodesResponse';
import { Observable, map, startWith } from 'rxjs';
import { PublicService } from '../_services/public.service';
import { MatIconRegistry } from '@angular/material/icon';
import { DomSanitizer } from '@angular/platform-browser';
import { PassengerCount } from '../_models/Flights/PassengerCount';

enum FlightClassEnum {
  ECONOMY = 0,
  PREMIUM_ECONOMY,
  BUSINESS,
  FIRST
};

@Component({
  selector: 'app-search-flights',
  templateUrl: './search-flights.component.html',
  styleUrls: ['./search-flights.component.css']
})
export class SearchFlightsComponent {
  @ViewChild(ShowFlightoffersComponent) child!: ShowFlightoffersComponent;
  passengerTypes = ["Adults (18+)", "Children (2-17)", "Infants (0-2)"]
  passengers = [1, 0, 0]
  classes = ['Economy class', 'Premium economy', 'Business class', 'First class'];
  range = new FormGroup({
    start: new FormControl<Date | null>(null),
    end: new FormControl<Date | null>(null),
  });

  model: SearchFlightsRequest = {
    travelType: 0,
    originLocationCode: '',
    destinationLocationCode: '',
    departureDate: '',
    returnDate: '',
    departureDateVar: this.range.value.start,
    returnDateVar: this.range.value.end,
    adults: 1,
    children: 0,
    infants: 0,
    travelClass: this.classes[0],
    travelClassVar: this.classes[0],
    isDirect: false
  }

  passengerCount: PassengerCount = { adults: 0, children: 0, infants: 0 };
  carrierCodes: String[] = [];
  isLoading: boolean = false;
  carrierCodeResponse: CarrierCodesResponse = { data: [] };
  flightsResponses: FlightResponses = { count: 0, data: [] };
  formSubmitted = false;
  currentYear = new Date().getFullYear();
  currentMonth = new Date().getMonth();
  currentDate = new Date().getDate();
  minDate = new Date(this.currentYear, this.currentMonth, this.currentDate)
  maxDate = new Date(this.currentYear + 1, this.currentMonth, this.currentDate);
  private timeout?: number;

  // Search ng control
  myControlFrom = new FormControl('');
  myControlTo = new FormControl('');
  optionsTo: string[] = [];
  optionsFrom: string[] = [];
  minSearchLength: number = 2;
  filteredOptionsFrom: Observable<string[]>;
  filteredOptionsTo: Observable<string[]>;
  regex = /, (\w+) -/

  constructor(private _flightService: FlightService, private _publicService: PublicService, iconRegistry: MatIconRegistry, sanitizer: DomSanitizer) {
    iconRegistry.addSvgIcon('plus-icon', sanitizer.bypassSecurityTrustResourceUrl('./assets/images/plus.svg'));
    iconRegistry.addSvgIcon('minus-icon', sanitizer.bypassSecurityTrustResourceUrl('./assets/images/minus.svg'))
  }

  resetAll() {
    this.child.reset()
    this.formSubmitted = false;
    this.isLoading = false;
  }

  inputSearchFrom(searchString: any): void {
    window.clearTimeout(this.timeout);

    this.clearOptions();
    this.setDropdown();

    if (searchString.target.value.length > this.minSearchLength) {
      this.timeout = window.setTimeout(() => this.makeSearchCallFrom(searchString.target.value), 500);
    }
  }

  inputSearchTo(searchString: any): void {
    window.clearTimeout(this.timeout);

    this.clearOptions();
    this.setDropdown();

    if (searchString.target.value.length > this.minSearchLength) {
      this.timeout = window.setTimeout(() => this.makeSearchCallTo(searchString.target.value), 500);
    }
  }

  makeSearchCallFrom(search: string): void {
    this._publicService.getCityname(search).subscribe(response => {
      this.clearOptions();

      response.city.forEach((x) => {
        this.optionsFrom.push(x.city + ", " + x.airportIcao + " - " + x.airportName);
      })

      this.setDropdown();
    })

  }

  makeSearchCallTo(search: string): void {
    this._publicService.getCityname(search).subscribe(response => {
      this.clearOptions();

      response.city.forEach((x) => {
        this.optionsTo.push(x.city + ", " + x.airportIcao + " - " + x.airportName);
      })

      this.setDropdown();
    })
  }

  clearOptions(): void {
    this.optionsFrom = [];
    this.optionsTo = [];
  }

  setDropdown(): void {
    this.filteredOptionsFrom = this.myControlFrom.valueChanges.pipe(
      startWith(''),
      map(value => this.filterFrom(value || '')),
    );

    this.filteredOptionsTo = this.myControlTo.valueChanges.pipe(
      startWith(''),
      map(value => this.filterTo(value || '')),
    );
  }

  private filterFrom(value: string): string[] {
    const filterValue = value.toLowerCase();

    return this.optionsFrom.filter(option => option.toLowerCase().includes(filterValue));
  }

  private filterTo(value: string): string[] {
    const filterValue = value.toLowerCase();

    return this.optionsTo.filter(option => option.toLowerCase().includes(filterValue));
  }

  submitForm(form: NgForm) {
    if (this.formSubmitted) {
      this.resetAll();
    }

    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.isLoading = true;

      this.replaceDestinationStrings();

      if (this.model.departureDateVar != null) {
        this.model.departureDate = this.formatDate(this.model.departureDateVar);
      }

      if (this.model.returnDateVar != null) {
        this.model.returnDate = this.formatDate(this.model.returnDateVar);
      }

      this.model.adults = this.passengerCount.adults = this.passengers[0];
      this.model.children = this.passengerCount.children = this.passengers[1];
      this.model.infants = this.passengerCount.infants = this.passengers[2];
      this.model.travelClass = FlightClassEnum[this.classes.indexOf(this.model.travelClassVar)];

      if (this.model.travelType == 1) this.model.returnDate = '';

      this._flightService.getFlights(this.model).subscribe({
        next: response => {
          this.flightsResponses = response;
          this.findAllUniqueCarrierCodes();
          this.swapCarrierCodeForCompanyName();
        }, error: err => {
          this.isLoading = false;
          console.log(err)
        }
      })
    }
  }

  replaceDestinationStrings(): void {
    let match = this.model.originLocationCode.match(this.regex);

    if (match != null) {
      this.model.originLocationCode = match[1];
    }

    match = this.model.destinationLocationCode.match(this.regex);

    if (match != null) {
      this.model.destinationLocationCode = match[1];
    }
  }

  findAllUniqueCarrierCodes(): void {
    this.flightsResponses.data.forEach(flightResponse => {
      flightResponse.itineraries.forEach(iti => {
        iti.segments.forEach(seg => {
          if (!this.carrierCodes.includes(seg.carrierCode)) this.carrierCodes.push(seg.carrierCode);
        })
      })
    });
  }

  swapCarrierCodeForCompanyName(): void {
    this._flightService.getCarriers(this.carrierCodes.join(",")).subscribe(response => {
      this.carrierCodeResponse = response;

      this.flightsResponses.data.forEach(data => {
        data.itineraries.forEach(iti => {
          iti.segments.forEach(seg => {
            this.carrierCodeResponse.data.forEach(data => {
              if (seg.carrierCode == data.iataCode) seg.carrierName = data.businessName;
            })
          })
        })
      })
      this.formSubmitted = true;
      this.isLoading = false;
    });
  }

  formatDate(date: Date): string {
    let monthMod = '';
    let dateMod = '';

    if (date.getMonth() < 10) {
      monthMod = '0';
    }
    if (date.getDate() < 10) {
      dateMod = '0';
    }

    return date.getFullYear() + "-" + monthMod + (date.getMonth() + 1) + "-" + dateMod + date.getDate();
  }

  ngAfterViewChecked() {
    if (this.isLoading) {
      let mySpinner = document.getElementById('spinner');
      if (mySpinner != null) mySpinner.scrollIntoView({ behavior: 'smooth' })
    }
  }

  addOnePassengerType(passengerType: string): void {
    let upperLimit = 4;

    for (let index = 0; index < this.passengerTypes.length; index++) {
      if (index == 1) upperLimit = this.passengers[0] * 2;
      if (index == 2) upperLimit = this.passengers[0] / 2;

      if (passengerType == this.passengerTypes[index] && this.passengers[index] < upperLimit) {
        this.passengers[index] += 1;
      }
    }
  }

  subtractOnePassengerType(passengerType: string): void {
    let lowerLimit = 1;

    for (let index = 0; index < this.passengerTypes.length; index++) {
      if (index > 0) lowerLimit = 0;
      if (passengerType == this.passengerTypes[index] && this.passengers[index] > lowerLimit) {
        this.passengers[index] -= 1;

        if (index == 0) {
          if (this.passengers[1] > this.passengers[0] * 2) this.passengers[1] = this.passengers[0] * 2;
          if (this.passengers[2] > this.passengers[0] / 2) this.passengers[2] = Math.round(this.passengers[0] / 2)
        }
      }
    }
  }
}