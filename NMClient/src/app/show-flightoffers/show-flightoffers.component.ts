import { Component, Input, OnInit } from '@angular/core';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightResponses } from '../_models/Flights/FlightResponses';
import { FlightService } from '../_services/flight.service';
import { PassengerCount } from '../_models/Flights/PassengerCount';

@Component({
  selector: 'app-show-flightoffers',
  templateUrl: './show-flightoffers.component.html',
  styleUrls: ['./show-flightoffers.component.css']
})
export class ShowFlightoffersComponent implements OnInit {
  @Input() offers!: FlightResponses;
  @Input() passengerCount: PassengerCount;
  // @Input() formSubmitted!: boolean;
  isLoading: boolean = false;
  flightInfo = {} as FlightInfoResponse;
  flightChosen: boolean = false;
  flightChosenHasResponse: boolean = false;
  moreOffers: boolean = false;
  shownOffers: number = 10;
  private paginationCount: number = 10;


  constructor(private _flightService: FlightService) { }

  ngOnInit(): void {
    console.log(this.offers.data.length + ' - ' + this.paginationCount);
    console.log(this.moreOffers)
    this.offers.data.length > this.paginationCount ? this.moreOffers = true : this.moreOffers = false;
    console.log(this.moreOffers)

    const mySpinner = document.getElementById('spinner');
    if (mySpinner != null) mySpinner.scrollIntoView({ behavior: 'smooth' });
  }

  bookFlight(id: string) {
    this.isLoading = true;
    this.flightChosen = true;

    this._flightService.getFlightInfo(this.offers.data[parseInt(id) - 1]).subscribe({
      next: response => {
        this.flightChosenHasResponse = true;
        this.flightInfo = response;
        this.isLoading = false;
      },
      error: err => {
        this.flightChosen = false;
        this.isLoading = false;
      }
    })
  }

  showMoreOffers() {
    if (this.shownOffers + this.paginationCount > this.offers.data.length) {
      this.shownOffers = this.offers.data.length;
      this.moreOffers = false;
    }
    else this.shownOffers += this.paginationCount;
  }

  reset() {
    this.flightChosen = false;
    this.shownOffers = this.paginationCount;
    this.moreOffers = true;
  }

  ngAfterViewChecked() {
    if (this.isLoading) {
      let mySpinner = document.getElementById('spinner');
      if (mySpinner != null) mySpinner.scrollIntoView({ behavior: 'smooth' })
    }
  }
}
