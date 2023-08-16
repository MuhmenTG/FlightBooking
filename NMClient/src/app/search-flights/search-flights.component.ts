import { Component, ViewChild } from '@angular/core';
import { FormControl, NgForm } from '@angular/forms';
import { ShowFlightoffersComponent } from '../show-flightoffers/show-flightoffers.component';
import { FlightResponses } from '../_models/Flights/FlightResponses';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightService } from '../_services/flight.service';
import { CarrierCodesResponse } from '../_models/Flights/CarrierCodesResponse';
import { Observable, map, startWith } from 'rxjs';
import { PublicService } from '../_services/public.service';

@Component({
  selector: 'app-search-flights',
  templateUrl: './search-flights.component.html',
  styleUrls: ['./search-flights.component.css']
})
export class SearchFlightsComponent {
  @ViewChild(ShowFlightoffersComponent) child!: ShowFlightoffersComponent;
  classes = ['First class', 'Business class', 'Economy class']
  adults = [1, 2, 3, 4, 5]
  carrierCodes: String[] = [];
  isResults: boolean = true;
  carrierCodeResponse: CarrierCodesResponse = { data: [] };
  model: SearchFlightsRequest = { travelType: 0, originLocationCode: '', destinationLocationCode: '', departureDate: '', returnDate: '', adults: this.adults[0], travelClass: "ECONOMY" }
  // this.classes[0]
  flightsResponses: FlightResponses = { count: 0, data: [] };
  formSubmitted = false;
  todayDate = Date.now();
  private timeout?: number;
  regex = /, (\w+) -/

  // Search ng control
  myControlFrom = new FormControl('');
  myControlTo = new FormControl('');
  options: string[] = [];
  filteredOptionsFrom: Observable<string[]>;
  filteredOptionsTo: Observable<string[]>;

  constructor(private _flightService: FlightService, private _publicService: PublicService) { }

  resetAll() {
    this.child.reset()
    this.formSubmitted = false;
    this.isResults = true;
  }

  submitForm(form: NgForm) {
    if (this.formSubmitted) {
      this.resetAll();
    }

    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this.isResults = false;
      this.replaceDestinationStrings();
      if (this.model.travelType == 1) this.model.returnDate = "";
      this._flightService.getFlights(this.model).subscribe(response => {
        this.flightsResponses = response;
        this.findAllUniqueCarrierCodes();
        this.swapCarrierCodeForCompanyName();
        this.formSubmitted = true;
        this.isResults = true;
      })
    }
  }

  replaceDestinationStrings() {
    let match = this.model.originLocationCode.match(this.regex);
    console.log(match);
    if(match != null){
      this.model.originLocationCode = match[1];
    }

    match = this.model.destinationLocationCode.match(this.regex);

    if(match != null) {
      this.model.destinationLocationCode = match[1];
    }
  }

  findAllUniqueCarrierCodes() {
    this.flightsResponses.data.forEach(flightResponse => {
      flightResponse.itineraries.forEach(iti => {
        iti.segments.forEach(seg => {
          if (!this.carrierCodes.includes(seg.carrierCode)) this.carrierCodes.push(seg.carrierCode);
        })
      })
    });
  }

  swapCarrierCodeForCompanyName() {
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
    });
  }

  setDropdown() {
    this.filteredOptionsFrom = this.myControlFrom.valueChanges.pipe(
      startWith(''),
      map(value => this._filter(value || '')),
    );

    this.filteredOptionsTo = this.myControlTo.valueChanges.pipe(
      startWith(''),
      map(value => this._filter(value || '')),
    );
  }

  private _filter(value: string): string[] {
    const filterValue = value.toLowerCase();

    return this.options.filter(option => option.toLowerCase().includes(filterValue));
  }

  inputSearch(searchString: any) {
    window.clearTimeout(this.timeout);
    this.options = [];
    this.setDropdown();
    
    if (searchString.target.value != ""){
      this.timeout = window.setTimeout(() => this.makeSearchCall(searchString.target.value), 500);
    }
  }

  makeSearchCall(search: string) {
    this._publicService.getCityname(search).subscribe(response => {
      console.log(response);
      this.options = [];
      response.city.forEach((x) => {
        this.options.push(x.city + ", " + x.airportIcao + " - " + x.airportName);
      })

      this.setDropdown();
    })
  }

  focus(){
    this.options = [];
    this.setDropdown()
  }
}