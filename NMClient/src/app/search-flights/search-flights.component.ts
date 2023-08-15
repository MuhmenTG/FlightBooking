import { Component, ViewChild } from '@angular/core';
import { NgForm } from '@angular/forms';
import { ShowFlightoffersComponent } from '../show-flightoffers/show-flightoffers.component';
import { FlightResponses } from '../_models/Flights/FlightResponses';
import { SearchFlightsRequest } from '../_models/Flights/SearchFlightsRequest';
import { FlightService } from '../_services/flight.service';
import { CarrierCodesResponse } from '../_models/Flights/CarrierCodesResponse';

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

  constructor(private _flightService: FlightService) { }

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
}