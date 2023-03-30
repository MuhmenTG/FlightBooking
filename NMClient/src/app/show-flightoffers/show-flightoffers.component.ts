import { Component, Input, OnInit } from '@angular/core';
import { CustomerInfo } from '../_models/CustomerInfo';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightResponse } from '../_models/Flights/FlightResponse';
import { FlightService } from '../_services/flight.service';
import { Carrier } from '../_models/Flights/Carrier';

@Component({
  selector: 'app-show-flightoffers',
  templateUrl: './show-flightoffers.component.html',
  styleUrls: ['./show-flightoffers.component.css']
})
export class ShowFlightoffersComponent {
  @Input() offers!: FlightResponse[];
  flightInfo = {} as FlightInfoResponse

  flightChosen: boolean = false;

  constructor(private _flightService: FlightService) { }

  chooseFlight(id: string) {
    this._flightService.getFlightInfo(this.offers[parseInt(id) - 1]).subscribe(info => {
      this.flightChosen = true;
      this.flightInfo = info;
    })
  }

  getCarrier(carrierCode: string) {
    let carrier: Carrier;
    // DANGEROUS CODE - DO NOT USE
    // this._flightService.getCarrier(carrierCode).subscribe(x => {
    //   x.forEach(carrierInfo => {
    //     carrier = carrierInfo;
    //     return carrier.commonName;
    //   });
    // });
  }
}